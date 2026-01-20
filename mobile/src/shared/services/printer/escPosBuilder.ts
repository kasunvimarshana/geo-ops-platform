/**
 * ESC/POS Command Builder
 * Generates ESC/POS commands for thermal printers
 */

import { InvoicePrintData, ReceiptPrintData, JobSummaryPrintData } from './types';

/**
 * ESC/POS Command Constants
 */
const ESC = '\x1B';
const GS = '\x1D';
const LF = '\x0A';
const INIT = `${ESC}@`; // Initialize printer
const ALIGN_LEFT = `${ESC}a\x00`;
const ALIGN_CENTER = `${ESC}a\x01`;
const ALIGN_RIGHT = `${ESC}a\x02`;
const BOLD_ON = `${ESC}E\x01`;
const BOLD_OFF = `${ESC}E\x00`;
const FONT_SIZE_NORMAL = `${GS}!\x00`;
const FONT_SIZE_DOUBLE = `${GS}!\x11`;
const FONT_SIZE_LARGE = `${GS}!\x22`;
const CUT_PAPER = `${GS}V\x00`; // Full cut
const LINE_SEPARATOR = '--------------------------------';

/**
 * Format currency amount
 */
function formatCurrency(amount: number, currency: string = 'LKR'): string {
  return `${currency} ${amount.toFixed(2)}`;
}

/**
 * Format line for receipt (text + amount)
 */
function formatLine(label: string, value: string, width: number = 32): string {
  const totalLength = label.length + value.length;
  
  // If content exceeds width, truncate label
  if (totalLength >= width) {
    const maxLabelLength = width - value.length - 1;
    const truncatedLabel = maxLabelLength > 0 ? label.substring(0, maxLabelLength) : label;
    return truncatedLabel + ' ' + value;
  }
  
  const spaces = width - totalLength;
  return label + ' '.repeat(Math.max(spaces, 1)) + value;
}

/**
 * ESC/POS Command Builder Class
 */
export class EscPosBuilder {
  private commands: string = '';

  constructor() {
    this.init();
  }

  /**
   * Initialize printer
   */
  private init(): this {
    this.commands += INIT;
    return this;
  }

  /**
   * Add text
   */
  text(text: string): this {
    this.commands += text;
    return this;
  }

  /**
   * Add text with line feed
   */
  textLn(text: string = ''): this {
    this.commands += text + LF;
    return this;
  }

  /**
   * Set alignment
   */
  align(alignment: 'left' | 'center' | 'right'): this {
    switch (alignment) {
      case 'left':
        this.commands += ALIGN_LEFT;
        break;
      case 'center':
        this.commands += ALIGN_CENTER;
        break;
      case 'right':
        this.commands += ALIGN_RIGHT;
        break;
    }
    return this;
  }

  /**
   * Set bold
   */
  bold(enabled: boolean = true): this {
    this.commands += enabled ? BOLD_ON : BOLD_OFF;
    return this;
  }

  /**
   * Set font size
   */
  size(size: 'normal' | 'double' | 'large'): this {
    switch (size) {
      case 'normal':
        this.commands += FONT_SIZE_NORMAL;
        break;
      case 'double':
        this.commands += FONT_SIZE_DOUBLE;
        break;
      case 'large':
        this.commands += FONT_SIZE_LARGE;
        break;
    }
    return this;
  }

  /**
   * Add line separator
   */
  separator(): this {
    this.textLn(LINE_SEPARATOR);
    return this;
  }

  /**
   * Add line feed
   */
  feed(lines: number = 1): this {
    this.commands += LF.repeat(lines);
    return this;
  }

  /**
   * Cut paper
   */
  cut(): this {
    this.commands += CUT_PAPER;
    return this;
  }

  /**
   * Build and return commands
   */
  build(): string {
    return this.commands;
  }

  /**
   * Build invoice commands
   */
  static buildInvoice(data: InvoicePrintData): string {
    const builder = new EscPosBuilder();

    // Header
    builder
      .align('center')
      .size('double')
      .bold(true)
      .textLn('INVOICE')
      .size('normal')
      .bold(false)
      .textLn(data.invoiceNumber)
      .feed(1);

    // Customer details
    builder
      .align('left')
      .bold(true)
      .textLn('Customer:')
      .bold(false)
      .textLn(data.customerName);

    if (data.customerPhone) {
      builder.textLn(`Phone: ${data.customerPhone}`);
    }

    builder.textLn(`Date: ${data.date}`).feed(1);

    // Items
    builder.separator();
    builder.bold(true).textLn('Items:').bold(false);

    data.items.forEach((item) => {
      const qty = item.quantity ? `${item.quantity}x ` : '';
      builder.textLn(`${qty}${item.description}`);
      builder.align('right').textLn(formatCurrency(item.price, data.currency)).align('left');
    });

    builder.separator();

    // Totals
    builder
      .align('left')
      .textLn(formatLine('Subtotal:', formatCurrency(data.subtotal, data.currency)));

    if (data.tax && data.tax > 0) {
      builder.textLn(formatLine('Tax:', formatCurrency(data.tax, data.currency)));
    }

    if (data.discount && data.discount > 0) {
      builder.textLn(formatLine('Discount:', `-${formatCurrency(data.discount, data.currency)}`));
    }

    builder
      .separator()
      .bold(true)
      .size('double')
      .textLn(formatLine('TOTAL:', formatCurrency(data.total, data.currency)))
      .size('normal')
      .bold(false);

    // Notes
    if (data.notes) {
      builder.feed(1).textLn('Notes:').textLn(data.notes);
    }

    // Footer
    builder
      .feed(2)
      .align('center')
      .textLn('Thank you for your business!')
      .feed(3)
      .cut();

    return builder.build();
  }

  /**
   * Build receipt commands
   */
  static buildReceipt(data: ReceiptPrintData): string {
    const builder = new EscPosBuilder();

    // Header
    builder
      .align('center')
      .size('double')
      .bold(true)
      .textLn('RECEIPT')
      .size('normal')
      .bold(false)
      .textLn(data.receiptNumber)
      .feed(1);

    // Details
    builder
      .align('left')
      .textLn(`Customer: ${data.customerName}`)
      .textLn(`Date: ${data.date}`)
      .textLn(`Payment: ${data.paymentMethod}`)
      .feed(1);

    // Amount
    builder
      .separator()
      .bold(true)
      .size('large')
      .align('center')
      .textLn(formatCurrency(data.amount, data.currency))
      .size('normal')
      .bold(false)
      .align('left')
      .separator();

    // Notes
    if (data.notes) {
      builder.feed(1).textLn('Notes:').textLn(data.notes);
    }

    // Footer
    builder
      .feed(2)
      .align('center')
      .textLn('Thank you!')
      .feed(3)
      .cut();

    return builder.build();
  }

  /**
   * Build job summary commands
   */
  static buildJobSummary(data: JobSummaryPrintData): string {
    const builder = new EscPosBuilder();

    // Header
    builder
      .align('center')
      .size('double')
      .bold(true)
      .textLn('JOB SUMMARY')
      .size('normal')
      .bold(false)
      .textLn(data.jobNumber)
      .feed(1);

    // Job details
    builder
      .align('left')
      .separator()
      .textLn(`Customer: ${data.customerName}`)
      .textLn(`Driver: ${data.driver}`)
      .textLn(`Date: ${data.date}`)
      .textLn(`Status: ${data.status}`)
      .separator()
      .feed(1);

    // Land area
    builder
      .bold(true)
      .textLn('Land Area:')
      .size('double')
      .textLn(`${data.landArea} ${data.areaUnit}`)
      .size('normal')
      .bold(false)
      .feed(1);

    // Location
    builder.textLn('Location:').textLn(data.location).feed(1);

    // Notes
    if (data.notes) {
      builder.separator().textLn('Notes:').textLn(data.notes);
    }

    // Footer
    builder
      .feed(2)
      .align('center')
      .textLn('GPS Field Management')
      .feed(3)
      .cut();

    return builder.build();
  }
}
