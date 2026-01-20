/**
 * ESC/POS Command Builder
 * 
 * Builds ESC/POS commands for thermal printers.
 * Supports text formatting, alignment, barcodes, and QR codes.
 */

import { Buffer } from 'buffer';
import type { Invoice, Payment, InvoiceItem } from '../../types';

// ESC/POS Commands
const ESC = 0x1b;
const GS = 0x1d;

export class EscPosBuilder {
  private buffer: number[] = [];

  /**
   * Initialize printer
   */
  initialize(): this {
    this.buffer.push(ESC, 0x40);
    return this;
  }

  /**
   * Add text
   */
  text(content: string): this {
    const encoder = new TextEncoder();
    const encoded = encoder.encode(content + '\n');
    this.buffer.push(...Array.from(encoded));
    return this;
  }

  /**
   * Set text alignment
   */
  align(alignment: 'left' | 'center' | 'right'): this {
    const alignmentCode = {
      left: 0x00,
      center: 0x01,
      right: 0x02,
    }[alignment];

    this.buffer.push(ESC, 0x61, alignmentCode);
    return this;
  }

  /**
   * Set bold on
   */
  bold(): this {
    this.buffer.push(ESC, 0x45, 0x01);
    return this;
  }

  /**
   * Set bold off
   */
  boldOff(): this {
    this.buffer.push(ESC, 0x45, 0x00);
    return this;
  }

  /**
   * Set underline on
   */
  underline(): this {
    this.buffer.push(ESC, 0x2d, 0x01);
    return this;
  }

  /**
   * Set underline off
   */
  underlineOff(): this {
    this.buffer.push(ESC, 0x2d, 0x00);
    return this;
  }

  /**
   * Set character size
   * @param width 1-8
   * @param height 1-8
   */
  size(width: number, height: number): this {
    const size = ((width - 1) << 4) | (height - 1);
    this.buffer.push(GS, 0x21, size);
    return this;
  }

  /**
   * Feed paper
   * @param lines Number of lines to feed
   */
  feed(lines: number = 1): this {
    this.buffer.push(ESC, 0x64, lines);
    return this;
  }

  /**
   * Cut paper
   */
  cut(): this {
    this.buffer.push(GS, 0x56, 0x00);
    return this;
  }

  /**
   * Partial cut paper
   */
  partialCut(): this {
    this.buffer.push(GS, 0x56, 0x01);
    return this;
  }

  /**
   * Print QR code
   * @param data Data to encode in QR code
   */
  qr(data: string): this {
    const encoder = new TextEncoder();
    const qrData = encoder.encode(data);
    const size = qrData.length + 3;

    // Select QR code model
    this.buffer.push(GS, 0x28, 0x6b, 0x04, 0x00, 0x31, 0x41, 0x32, 0x00);

    // Set QR code size (1-16)
    this.buffer.push(GS, 0x28, 0x6b, 0x03, 0x00, 0x31, 0x43, 0x05);

    // Set error correction level (L=0x30, M=0x31, Q=0x32, H=0x33)
    this.buffer.push(GS, 0x28, 0x6b, 0x03, 0x00, 0x31, 0x45, 0x31);

    // Store QR code data
    this.buffer.push(
      GS,
      0x28,
      0x6b,
      size & 0xff,
      (size >> 8) & 0xff,
      0x31,
      0x50,
      0x30,
      ...Array.from(qrData)
    );

    // Print QR code
    this.buffer.push(GS, 0x28, 0x6b, 0x03, 0x00, 0x31, 0x51, 0x30);

    return this;
  }

  /**
   * Print barcode
   * @param data Barcode data
   * @param type Barcode type (CODE39, CODE128, etc.)
   */
  barcode(data: string, type: 'CODE39' | 'CODE128' | 'EAN13' = 'CODE128'): this {
    const encoder = new TextEncoder();
    const barcodeData = encoder.encode(data);

    const barcodeType = {
      CODE39: 0x04,
      CODE128: 0x49,
      EAN13: 0x02,
    }[type];

    // Set barcode height
    this.buffer.push(GS, 0x68, 0x64);

    // Set barcode width
    this.buffer.push(GS, 0x77, 0x02);

    // Print barcode
    this.buffer.push(GS, 0x6b, barcodeType, barcodeData.length, ...Array.from(barcodeData));

    return this;
  }

  /**
   * Set line spacing
   * @param spacing Spacing in dots (0-255)
   */
  lineSpacing(spacing: number): this {
    this.buffer.push(ESC, 0x33, spacing);
    return this;
  }

  /**
   * Reset line spacing to default
   */
  resetLineSpacing(): this {
    this.buffer.push(ESC, 0x32);
    return this;
  }

  /**
   * Set character spacing
   * @param spacing Spacing in dots (0-255)
   */
  characterSpacing(spacing: number): this {
    this.buffer.push(ESC, 0x20, spacing);
    return this;
  }

  /**
   * Enable/disable double strike
   */
  doubleStrike(enable: boolean = true): this {
    this.buffer.push(ESC, 0x47, enable ? 0x01 : 0x00);
    return this;
  }

  /**
   * Set font (A or B)
   */
  font(font: 'A' | 'B'): this {
    this.buffer.push(ESC, 0x4d, font === 'A' ? 0x00 : 0x01);
    return this;
  }

  /**
   * Enable/disable reverse printing (white text on black background)
   */
  reverse(enable: boolean = true): this {
    this.buffer.push(GS, 0x42, enable ? 0x01 : 0x00);
    return this;
  }

  /**
   * Set upside-down printing
   */
  upsideDown(enable: boolean = true): this {
    this.buffer.push(ESC, 0x7b, enable ? 0x01 : 0x00);
    return this;
  }

  /**
   * Print and feed n lines
   */
  printAndFeed(lines: number = 1): this {
    this.buffer.push(ESC, 0x4a, lines);
    return this;
  }

  /**
   * Draw horizontal line
   */
  horizontalLine(char: string = '-', length: number = 37): this {
    this.text(char.repeat(length));
    return this;
  }

  /**
   * Print formatted row with left and right text
   */
  row(left: string, right: string, totalWidth: number = 37): this {
    const spacing = totalWidth - left.length - right.length;
    const row = left + ' '.repeat(Math.max(0, spacing)) + right;
    this.text(row);
    return this;
  }

  /**
   * Print table row with columns
   */
  tableRow(columns: string[], widths: number[]): this {
    if (columns.length !== widths.length) {
      throw new Error('Column count must match width count');
    }

    let row = '';
    for (let i = 0; i < columns.length; i++) {
      const col = columns[i].substring(0, widths[i]);
      row += col.padEnd(widths[i]);
    }

    this.text(row);
    return this;
  }

  /**
   * Open cash drawer (if connected)
   */
  openCashDrawer(): this {
    this.buffer.push(ESC, 0x70, 0x00, 0x32, 0xfa);
    return this;
  }

  /**
   * Build and return buffer
   */
  build(): Buffer {
    return Buffer.from(this.buffer);
  }

  /**
   * Get raw buffer array
   */
  getRawBuffer(): number[] {
    return this.buffer;
  }

  /**
   * Clear buffer
   */
  clear(): this {
    this.buffer = [];
    return this;
  }

  /**
   * Get buffer length
   */
  length(): number {
    return this.buffer.length;
  }
}

/**
 * Helper function to create formatted invoice
 */
export const createInvoiceFormat = (invoice: Invoice): Buffer => {
  const builder = new EscPosBuilder();

  builder
    .initialize()
    .align('center')
    .size(2, 2)
    .bold()
    .text(invoice.organization_name || 'GEO OPS PLATFORM')
    .boldOff()
    .size(1, 1)
    .text(invoice.customer_address || '')
    .text(invoice.customer_phone || '')
    .feed(1)
    .horizontalLine('=', 37)
    .align('left')
    .bold()
    .text('INVOICE')
    .boldOff()
    .horizontalLine('=', 37)
    .row('Invoice #:', invoice.invoice_number)
    .row('Date:', invoice.invoice_date)
    .row('Customer:', invoice.customer_name)
    .row('Phone:', invoice.customer_phone || '')
    .horizontalLine('-', 37)
    .bold()
    .tableRow(['Item', 'Qty', 'Price', 'Total'], [15, 4, 8, 10])
    .boldOff()
    .horizontalLine('-', 37);

  // Add items
  invoice.items?.forEach((item: InvoiceItem) => {
    builder.tableRow(
      [item.description, item.quantity.toString(), item.unit_price.toFixed(2), item.total.toFixed(2)],
      [15, 4, 8, 10]
    );
  });

  builder
    .horizontalLine('-', 37)
    .align('right')
    .text(`Subtotal: ${invoice.subtotal.toFixed(2)}`)
    .text(`Tax: ${invoice.tax_amount.toFixed(2)}`)
    .horizontalLine('-', 37)
    .bold()
    .size(1, 2)
    .text(`TOTAL: ${invoice.currency || 'LKR'} ${invoice.total_amount.toFixed(2)}`)
    .size(1, 1)
    .boldOff()
    .horizontalLine('-', 37)
    .align('center')
    .text('Thank You!')
    .feed(1)
    .qr(invoice.invoice_number)
    .feed(3)
    .cut();

  return builder.build();
};

/**
 * Helper function to create formatted receipt
 */
export const createReceiptFormat = (receipt: Payment): Buffer => {
  const builder = new EscPosBuilder();

  builder
    .initialize()
    .align('center')
    .size(2, 2)
    .bold()
    .text('RECEIPT')
    .boldOff()
    .size(1, 1)
    .feed(1)
    .horizontalLine('=', 37)
    .align('left')
    .row('Receipt #:', receipt.payment_number)
    .row('Date:', receipt.payment_date)
    .row('Invoice #:', receipt.invoice_id?.toString() || 'N/A')
    .horizontalLine('-', 37)
    .bold()
    .size(1, 2)
    .text(`Amount: ${receipt.currency || 'LKR'} ${receipt.amount.toFixed(2)}`)
    .size(1, 1)
    .boldOff()
    .row('Method:', receipt.payment_method.replace('_', ' ').toUpperCase())
    .row('Received By:', receipt.received_by_name || 'N/A')
    .horizontalLine('-', 37)
    .align('center')
    .text('Thank You!')
    .feed(1)
    .qr(receipt.payment_number)
    .feed(3)
    .cut();

  return builder.build();
};
