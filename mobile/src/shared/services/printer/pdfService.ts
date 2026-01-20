/**
 * PDF Fallback Service
 * Generates PDF receipts/invoices when Bluetooth printing is unavailable
 */

import * as Print from 'expo-print';
import * as Sharing from 'expo-sharing';
import type {
  InvoicePrintData,
  ReceiptPrintData,
  JobSummaryPrintData,
} from './types';

/**
 * Generate HTML for invoice PDF
 */
function generateInvoiceHTML(data: InvoicePrintData): string {
  const itemsHtml = data.items
    .map(
      (item) => `
        <tr>
          <td>${item.quantity ? `${item.quantity}x ` : ''}${item.description}</td>
          <td style="text-align: right;">${data.currency} ${item.price.toFixed(2)}</td>
        </tr>
      `
    )
    .join('');

  return `
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <style>
        body {
          font-family: Arial, sans-serif;
          margin: 20px;
          font-size: 14px;
        }
        .header {
          text-align: center;
          margin-bottom: 30px;
          border-bottom: 2px solid #333;
          padding-bottom: 10px;
        }
        .header h1 {
          margin: 10px 0;
          font-size: 28px;
        }
        .info {
          margin-bottom: 20px;
        }
        .info p {
          margin: 5px 0;
        }
        table {
          width: 100%;
          border-collapse: collapse;
          margin: 20px 0;
        }
        th, td {
          border: 1px solid #ddd;
          padding: 10px;
          text-align: left;
        }
        th {
          background-color: #f2f2f2;
          font-weight: bold;
        }
        .totals {
          margin-top: 20px;
        }
        .totals table {
          width: 50%;
          margin-left: auto;
        }
        .totals td {
          border: none;
          padding: 5px 10px;
        }
        .total-row {
          font-weight: bold;
          font-size: 18px;
          border-top: 2px solid #333 !important;
        }
        .notes {
          margin-top: 30px;
          padding: 15px;
          background-color: #f9f9f9;
          border-left: 3px solid #333;
        }
        .footer {
          text-align: center;
          margin-top: 40px;
          padding-top: 20px;
          border-top: 1px solid #ddd;
          font-size: 12px;
          color: #666;
        }
      </style>
    </head>
    <body>
      <div class="header">
        <h1>INVOICE</h1>
        <p>${data.invoiceNumber}</p>
      </div>

      <div class="info">
        <p><strong>Customer:</strong> ${data.customerName}</p>
        ${data.customerPhone ? `<p><strong>Phone:</strong> ${data.customerPhone}</p>` : ''}
        <p><strong>Date:</strong> ${data.date}</p>
      </div>

      <table>
        <thead>
          <tr>
            <th>Description</th>
            <th style="text-align: right; width: 150px;">Amount</th>
          </tr>
        </thead>
        <tbody>
          ${itemsHtml}
        </tbody>
      </table>

      <div class="totals">
        <table>
          <tr>
            <td>Subtotal:</td>
            <td style="text-align: right;">${data.currency} ${data.subtotal.toFixed(2)}</td>
          </tr>
          ${
            data.tax && data.tax > 0
              ? `
          <tr>
            <td>Tax:</td>
            <td style="text-align: right;">${data.currency} ${data.tax.toFixed(2)}</td>
          </tr>
          `
              : ''
          }
          ${
            data.discount && data.discount > 0
              ? `
          <tr>
            <td>Discount:</td>
            <td style="text-align: right;">-${data.currency} ${data.discount.toFixed(2)}</td>
          </tr>
          `
              : ''
          }
          <tr class="total-row">
            <td>TOTAL:</td>
            <td style="text-align: right;">${data.currency} ${data.total.toFixed(2)}</td>
          </tr>
        </table>
      </div>

      ${
        data.notes
          ? `
      <div class="notes">
        <strong>Notes:</strong><br>
        ${data.notes}
      </div>
      `
          : ''
      }

      <div class="footer">
        <p>Thank you for your business!</p>
        <p>GPS Field Management Platform</p>
      </div>
    </body>
    </html>
  `;
}

/**
 * Generate HTML for receipt PDF
 */
function generateReceiptHTML(data: ReceiptPrintData): string {
  return `
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <style>
        body {
          font-family: Arial, sans-serif;
          margin: 20px;
          font-size: 14px;
        }
        .header {
          text-align: center;
          margin-bottom: 30px;
          border-bottom: 2px solid #333;
          padding-bottom: 10px;
        }
        .header h1 {
          margin: 10px 0;
          font-size: 28px;
        }
        .info {
          margin-bottom: 20px;
        }
        .info p {
          margin: 8px 0;
        }
        .amount-box {
          background-color: #f2f2f2;
          border: 2px solid #333;
          padding: 20px;
          text-align: center;
          margin: 30px 0;
          border-radius: 5px;
        }
        .amount-box .amount {
          font-size: 32px;
          font-weight: bold;
          color: #333;
        }
        .notes {
          margin-top: 30px;
          padding: 15px;
          background-color: #f9f9f9;
          border-left: 3px solid #333;
        }
        .footer {
          text-align: center;
          margin-top: 40px;
          padding-top: 20px;
          border-top: 1px solid #ddd;
          font-size: 12px;
          color: #666;
        }
      </style>
    </head>
    <body>
      <div class="header">
        <h1>RECEIPT</h1>
        <p>${data.receiptNumber}</p>
      </div>

      <div class="info">
        <p><strong>Customer:</strong> ${data.customerName}</p>
        <p><strong>Date:</strong> ${data.date}</p>
        <p><strong>Payment Method:</strong> ${data.paymentMethod}</p>
      </div>

      <div class="amount-box">
        <div class="amount">${data.currency} ${data.amount.toFixed(2)}</div>
      </div>

      ${
        data.notes
          ? `
      <div class="notes">
        <strong>Notes:</strong><br>
        ${data.notes}
      </div>
      `
          : ''
      }

      <div class="footer">
        <p>Thank you!</p>
        <p>GPS Field Management Platform</p>
      </div>
    </body>
    </html>
  `;
}

/**
 * Generate HTML for job summary PDF
 */
function generateJobSummaryHTML(data: JobSummaryPrintData): string {
  return `
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <style>
        body {
          font-family: Arial, sans-serif;
          margin: 20px;
          font-size: 14px;
        }
        .header {
          text-align: center;
          margin-bottom: 30px;
          border-bottom: 2px solid #333;
          padding-bottom: 10px;
        }
        .header h1 {
          margin: 10px 0;
          font-size: 28px;
        }
        .info-grid {
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 15px;
          margin-bottom: 30px;
        }
        .info-item {
          padding: 10px;
          background-color: #f9f9f9;
          border-radius: 5px;
        }
        .info-item strong {
          display: block;
          color: #666;
          font-size: 12px;
          margin-bottom: 5px;
        }
        .area-box {
          background-color: #e8f4f8;
          border: 2px solid #0077b6;
          padding: 20px;
          text-align: center;
          margin: 30px 0;
          border-radius: 5px;
        }
        .area-box .area {
          font-size: 36px;
          font-weight: bold;
          color: #0077b6;
        }
        .area-box .label {
          font-size: 14px;
          color: #666;
          margin-top: 5px;
        }
        .notes {
          margin-top: 30px;
          padding: 15px;
          background-color: #f9f9f9;
          border-left: 3px solid #333;
        }
        .footer {
          text-align: center;
          margin-top: 40px;
          padding-top: 20px;
          border-top: 1px solid #ddd;
          font-size: 12px;
          color: #666;
        }
        .status {
          display: inline-block;
          padding: 5px 15px;
          border-radius: 20px;
          background-color: #4caf50;
          color: white;
          font-weight: bold;
          font-size: 12px;
          text-transform: uppercase;
        }
      </style>
    </head>
    <body>
      <div class="header">
        <h1>JOB SUMMARY</h1>
        <p>${data.jobNumber}</p>
      </div>

      <div class="info-grid">
        <div class="info-item">
          <strong>Customer</strong>
          ${data.customerName}
        </div>
        <div class="info-item">
          <strong>Driver</strong>
          ${data.driver}
        </div>
        <div class="info-item">
          <strong>Date</strong>
          ${data.date}
        </div>
        <div class="info-item">
          <strong>Status</strong>
          <span class="status">${data.status}</span>
        </div>
      </div>

      <div class="area-box">
        <div class="area">${data.landArea} ${data.areaUnit}</div>
        <div class="label">Total Land Area</div>
      </div>

      <div class="info-item">
        <strong>Location</strong>
        ${data.location}
      </div>

      ${
        data.notes
          ? `
      <div class="notes">
        <strong>Notes:</strong><br>
        ${data.notes}
      </div>
      `
          : ''
      }

      <div class="footer">
        <p>GPS Field Management Platform</p>
      </div>
    </body>
    </html>
  `;
}

class PDFService {
  /**
   * Generate and share invoice PDF
   */
  async generateInvoicePDF(data: InvoicePrintData): Promise<string> {
    try {
      const html = generateInvoiceHTML(data);
      const { uri } = await Print.printToFileAsync({ html });
      console.log('Invoice PDF generated:', uri);
      return uri;
    } catch (error) {
      console.error('Error generating invoice PDF:', error);
      throw new Error('Failed to generate invoice PDF');
    }
  }

  /**
   * Generate and share receipt PDF
   */
  async generateReceiptPDF(data: ReceiptPrintData): Promise<string> {
    try {
      const html = generateReceiptHTML(data);
      const { uri } = await Print.printToFileAsync({ html });
      console.log('Receipt PDF generated:', uri);
      return uri;
    } catch (error) {
      console.error('Error generating receipt PDF:', error);
      throw new Error('Failed to generate receipt PDF');
    }
  }

  /**
   * Generate and share job summary PDF
   */
  async generateJobSummaryPDF(data: JobSummaryPrintData): Promise<string> {
    try {
      const html = generateJobSummaryHTML(data);
      const { uri } = await Print.printToFileAsync({ html });
      console.log('Job summary PDF generated:', uri);
      return uri;
    } catch (error) {
      console.error('Error generating job summary PDF:', error);
      throw new Error('Failed to generate job summary PDF');
    }
  }

  /**
   * Share PDF file
   */
  async sharePDF(uri: string, filename: string = 'document.pdf'): Promise<void> {
    try {
      const isAvailable = await Sharing.isAvailableAsync();
      if (!isAvailable) {
        throw new Error('Sharing is not available on this device');
      }

      await Sharing.shareAsync(uri, {
        mimeType: 'application/pdf',
        dialogTitle: filename,
        UTI: 'com.adobe.pdf',
      });
    } catch (error) {
      console.error('Error sharing PDF:', error);
      throw new Error('Failed to share PDF');
    }
  }

  /**
   * Print PDF (system print dialog)
   */
  async printPDF(uri: string): Promise<void> {
    try {
      await Print.printAsync({ uri });
    } catch (error) {
      console.error('Error printing PDF:', error);
      throw new Error('Failed to print PDF');
    }
  }
}

// Export singleton instance
export const pdfService = new PDFService();
