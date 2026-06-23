from flask import Flask, request, jsonify
from escpos.printer import Usb
import os


app = Flask(__name__)


def money(value):
    try:
        return f"${float(value):.2f}"
    except (TypeError, ValueError):
        return "$0.00"


def line(p):
    p.text("--------------------------------\n")


def center(p, text=""):
    p.set(align="center")
    p.text(f"{text}\n")


def left_right(p, left, right, bold_left=False, bold_right=False):
    # Compact layout for 80mm printers.
    left = str(left)
    right = str(right)
    max_left = 24
    max_right = 11

    if len(left) > max_left:
        left = left[: max_left - 1] + "."
    if len(right) > max_right:
        right = right[: max_right - 1] + "."

    p.set(align="left")
    p.text(f"{left:<24}{right:>11}\n")


def print_account_ticket(p, data):
    cuenta = data.get("cuenta", {})
    tickets = data.get("tickets", [])
    pagos = data.get("pagos_aplicados", [])
    resumen = data.get("resumen", {})

    titulo = data.get("titulo", "MAX & CLEAN")
    subtitulo = data.get("subtitulo", "Estado de cuenta")

    p.set(align="center")
    p.text(f"{titulo}\n")
    p.text(f"{subtitulo}\n")
    if data.get("sucursal"):
        p.text(f"{data['sucursal']}\n")
    if data.get("fecha"):
        p.text(f"{data['fecha']}\n")

    qr_value = str(data.get("qr") or cuenta.get("id") or "")
    if qr_value:
        p.qr(qr_value, size=6)

    if data.get("numero_impreso"):
        p.text(f"#: {data['numero_impreso']}\n")
    elif cuenta.get("numero"):
        p.text(f"#: {cuenta['numero']}\n")

    line(p)

    p.set(align="left")
    left_right(p, "Cliente", cuenta.get("cliente", "Sin cliente"))
    if cuenta.get("whatsapp"):
        left_right(p, "Whatsapp", cuenta.get("whatsapp"))
    left_right(p, "Estatus", cuenta.get("estatus", "Sin estatus"))
    left_right(p, "Abierta", cuenta.get("abierta_en", "-"))

    line(p)
    center(p, "TICKETS INCLUIDOS")
    p.set(align="left")
    p.text("Ticket    Tipo      Total   Desc.  Pagado    Debe\n")

    for ticket in tickets:
        numero = f"#{ticket.get('numero', 'S/I')}"
        tipo = str(ticket.get("tipo", ""))
        total = money(ticket.get("total"))
        descuento = money(ticket.get("descuento"))
        pagado = money(ticket.get("pagado"))
        saldo = money(ticket.get("saldo"))

        p.text(
            f"{numero:<9}{tipo:<8}{total:>8}{descuento:>8}{pagado:>8}{saldo:>8}\n"
        )

    if not tickets:
        p.text("Sin tickets incluidos.\n")

    line(p)
    center(p, "PAGOS APLICADOS")
    p.set(align="left")

    if pagos:
        p.text("Fecha        Ticket   Método     Monto\n")
        for pago in pagos:
            fecha = str(pago.get("fecha", ""))
            ticket = f"#{pago.get('ticket', 'S/I')}"
            metodo = str(pago.get("metodo_pago", ""))
            monto = money(pago.get("monto"))
            p.text(f"{fecha:<12}{ticket:<8}{metodo:<10}{monto:>8}\n")
    else:
        p.set(align="center")
        p.text("Sin pagos registrados.\n")

    notas = cuenta.get("notas")
    if notas:
        line(p)
        p.set(align="left")
        p.text("Notas de la cuenta\n")
        p.text(f"{notas}\n")

    line(p)
    p.set(align="left")
    left_right(p, "Total antes de descuento", money(resumen.get("total_antes_descuento")))
    left_right(p, "Descuentos aplicados", f"-{money(resumen.get('descuentos_aplicados')).replace('$', '')}")
    left_right(p, "Total tickets", money(resumen.get("total_tickets")))
    left_right(p, "Total pagado", money(resumen.get("total_pagado")))
    p.set(align="left")
    p.text(f"SALDO: {money(resumen.get('saldo'))}\n")

    line(p)
    p.set(align="center")
    p.text("En caso de requerir factura, solicítela el día de su pago; de lo contrario, se integrará a la factura global del día.\n")
    p.text("Gracias por su preferencia.\n")
    p.text("Este comprobante resume los tickets agrupados en la cuenta.\n")


def print_legacy_ticket(p, data):
    p.set(align="center")
    p.text("MAXCLEAN\n")
    p.text(f"Ticket: {data.get('numero', 'S/I')}\n")

    for item in data.get("items", []):
        nombre = item.get("nombre", "Sin nombre")
        precio = item.get("precio", "0.00")
        p.set(align="left")
        p.text(f"{nombre} ${precio}\n")

    p.set(align="left")
    p.text(f"TOTAL: {money(data.get('total'))}\n")

    qr_value = str(data.get("qr") or "")
    if qr_value:
        p.qr(qr_value, size=6)


@app.route("/print", methods=["POST"])
def print_ticket():
    try:
        data = request.get_json(force=True, silent=False) or {}

        vendor_id = int(os.getenv("PRINTER_VENDOR_ID", "1048"))
        product_id = int(os.getenv("PRINTER_PRODUCT_ID", "20497"))
        printer_index = int(os.getenv("PRINTER_INDEX", "0"))
        in_ep = int(os.getenv("PRINTER_IN_EP", "130"))
        out_ep = int(os.getenv("PRINTER_OUT_EP", "1"))
        flip = os.getenv("PRINTER_FLIP", "false").lower() in {"1", "true", "yes"}

        p = Usb(vendor_id, product_id, printer_index, in_ep=in_ep, out_ep=out_ep)
        p.set(align="center", flip=flip)

        if data.get("tipo") == "cuenta" or "pagos_aplicados" in data or "resumen" in data:
            print_account_ticket(p, data)
        else:
            print_legacy_ticket(p, data)

        p.cut()
        p.close()

        return jsonify({"status": "ok"})

    except Exception as e:
        return jsonify({"status": "error", "msg": str(e)}), 500


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=int(os.getenv("PORT", "5000")))
