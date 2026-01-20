import { NativeModules, Platform } from 'react-native';

// Note: This requires react-native-bluetooth-escpos-printer package
// For iOS, you'll need to link it properly and add Bluetooth permissions
const { BluetoothManager, BluetoothEscposPrinter } = NativeModules;

export interface PrinterDevice {
  name: string;
  address: string;
}

/**
 * Bluetooth Printer Service
 * 
 * Handles Bluetooth printer connections and ESC/POS printing
 */
class PrinterService {
  async isBluetoothEnabled(): Promise<boolean> {
    try {
      return await BluetoothManager.isBluetoothEnabled();
    } catch (error) {
      console.error('Bluetooth check error:', error);
      return false;
    }
  }

  async enableBluetooth(): Promise<void> {
    if (Platform.OS === 'android') {
      await BluetoothManager.enableBluetooth();
    }
  }

  async scanDevices(): Promise<PrinterDevice[]> {
    try {
      const devices = await BluetoothManager.list();
      return devices.map((device: any) => ({
        name: device.name || 'Unknown Device',
        address: device.address,
      }));
    } catch (error) {
      console.error('Scan devices error:', error);
      return [];
    }
  }

  async connect(address: string): Promise<boolean> {
    try {
      await BluetoothEscposPrinter.connect(address);
      return true;
    } catch (error) {
      console.error('Connect error:', error);
      return false;
    }
  }

  async disconnect(): Promise<void> {
    try {
      await BluetoothEscposPrinter.disconnect();
    } catch (error) {
      console.error('Disconnect error:', error);
    }
  }

  async printInvoice(invoice: {
    invoice_number: string;
    customer_name: string;
    customer_phone: string;
    invoice_date: string;
    area_acres: number;
    area_hectares: number;
    rate_per_unit: number;
    subtotal: number;
    tax_amount: number;
    total_amount: number;
    organization_name: string;
  }): Promise<boolean> {
    try {
      await BluetoothEscposPrinter.printerAlign(
        BluetoothEscposPrinter.ALIGN.CENTER
      );
      
      await BluetoothEscposPrinter.printText(
        `${invoice.organization_name}\n`,
        { fontSize: 2, fontWeight: 1 }
      );
      
      await BluetoothEscposPrinter.printText('INVOICE\n\n', {
        fontSize: 1.5,
        fontWeight: 1,
      });

      await BluetoothEscposPrinter.printerAlign(
        BluetoothEscposPrinter.ALIGN.LEFT
      );

      await BluetoothEscposPrinter.printText(
        `Invoice No: ${invoice.invoice_number}\n`,
        {}
      );
      
      await BluetoothEscposPrinter.printText(
        `Date: ${invoice.invoice_date}\n\n`,
        {}
      );

      await BluetoothEscposPrinter.printText('Customer Details:\n', {
        fontWeight: 1,
      });
      
      await BluetoothEscposPrinter.printText(
        `Name: ${invoice.customer_name}\n`,
        {}
      );
      
      await BluetoothEscposPrinter.printText(
        `Phone: ${invoice.customer_phone}\n\n`,
        {}
      );

      await BluetoothEscposPrinter.printText('--------------------------------\n', {});

      await BluetoothEscposPrinter.printText('Service Details:\n', {
        fontWeight: 1,
      });
      
      await BluetoothEscposPrinter.printText(
        `Area: ${invoice.area_acres.toFixed(2)} acres\n`,
        {}
      );
      
      await BluetoothEscposPrinter.printText(
        `      ${invoice.area_hectares.toFixed(2)} hectares\n`,
        {}
      );
      
      await BluetoothEscposPrinter.printText(
        `Rate: Rs. ${invoice.rate_per_unit.toFixed(2)}\n\n`,
        {}
      );

      await BluetoothEscposPrinter.printText('--------------------------------\n', {});

      await BluetoothEscposPrinter.printText(
        `Subtotal:    Rs. ${invoice.subtotal.toFixed(2)}\n`,
        {}
      );
      
      await BluetoothEscposPrinter.printText(
        `Tax:         Rs. ${invoice.tax_amount.toFixed(2)}\n`,
        {}
      );

      await BluetoothEscposPrinter.printText('--------------------------------\n', {});

      await BluetoothEscposPrinter.printText(
        `TOTAL:       Rs. ${invoice.total_amount.toFixed(2)}\n`,
        { fontSize: 1.5, fontWeight: 1 }
      );

      await BluetoothEscposPrinter.printText('\n\n');

      await BluetoothEscposPrinter.printerAlign(
        BluetoothEscposPrinter.ALIGN.CENTER
      );
      
      await BluetoothEscposPrinter.printText('Thank you!\n\n\n', {});

      await BluetoothEscposPrinter.cutPaper();

      return true;
    } catch (error) {
      console.error('Print error:', error);
      return false;
    }
  }

  async printLandMeasurement(land: {
    name: string;
    customer_name: string;
    customer_phone: string;
    location_name: string;
    area_acres: number;
    area_hectares: number;
    measured_at: string;
    organization_name: string;
  }): Promise<boolean> {
    try {
      await BluetoothEscposPrinter.printerAlign(
        BluetoothEscposPrinter.ALIGN.CENTER
      );
      
      await BluetoothEscposPrinter.printText(
        `${land.organization_name}\n`,
        { fontSize: 2, fontWeight: 1 }
      );
      
      await BluetoothEscposPrinter.printText('LAND MEASUREMENT\n\n', {
        fontSize: 1.5,
        fontWeight: 1,
      });

      await BluetoothEscposPrinter.printerAlign(
        BluetoothEscposPrinter.ALIGN.LEFT
      );

      await BluetoothEscposPrinter.printText(`Land: ${land.name}\n`, {
        fontWeight: 1,
      });
      
      await BluetoothEscposPrinter.printText(
        `Location: ${land.location_name}\n`,
        {}
      );
      
      await BluetoothEscposPrinter.printText(
        `Date: ${land.measured_at}\n\n`,
        {}
      );

      await BluetoothEscposPrinter.printText('Customer:\n', { fontWeight: 1 });
      await BluetoothEscposPrinter.printText(`${land.customer_name}\n`, {});
      await BluetoothEscposPrinter.printText(`${land.customer_phone}\n\n`, {});

      await BluetoothEscposPrinter.printText('--------------------------------\n', {});

      await BluetoothEscposPrinter.printerAlign(
        BluetoothEscposPrinter.ALIGN.CENTER
      );
      
      await BluetoothEscposPrinter.printText('TOTAL AREA\n', {});
      
      await BluetoothEscposPrinter.printText(
        `${land.area_acres.toFixed(2)} Acres\n`,
        { fontSize: 2, fontWeight: 1 }
      );
      
      await BluetoothEscposPrinter.printText(
        `${land.area_hectares.toFixed(2)} Hectares\n\n`,
        { fontSize: 2, fontWeight: 1 }
      );

      await BluetoothEscposPrinter.printText('\n\n', {});

      await BluetoothEscposPrinter.cutPaper();

      return true;
    } catch (error) {
      console.error('Print error:', error);
      return false;
    }
  }
}

export const printerService = new PrinterService();
export default printerService;
