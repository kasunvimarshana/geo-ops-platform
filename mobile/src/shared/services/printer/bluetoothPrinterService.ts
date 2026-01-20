/**
 * Bluetooth Printer Service
 * Handles Bluetooth device discovery, connection, and printing
 */

import { Platform, PermissionsAndroid } from 'react-native';
import {
  BluetoothManager,
  BluetoothEscposPrinter,
} from 'react-native-bluetooth-escpos-printer';
import type {
  BluetoothDevice,
  PrintJob,
  PrintData,
  PrintOptions,
  InvoicePrintData,
  ReceiptPrintData,
  JobSummaryPrintData,
} from './types';
import { EscPosBuilder } from './escPosBuilder';

class BluetoothPrinterService {
  private connectedDevice: BluetoothDevice | null = null;
  private isDiscovering: boolean = false;

  /**
   * Request Bluetooth permissions (Android)
   */
  async requestPermissions(): Promise<boolean> {
    if (Platform.OS === 'android') {
      try {
        if (Platform.Version >= 31) {
          // Android 12+
          const granted = await PermissionsAndroid.requestMultiple([
            PermissionsAndroid.PERMISSIONS.BLUETOOTH_SCAN,
            PermissionsAndroid.PERMISSIONS.BLUETOOTH_CONNECT,
          ]);

          return (
            granted['android.permission.BLUETOOTH_SCAN'] === PermissionsAndroid.RESULTS.GRANTED &&
            granted['android.permission.BLUETOOTH_CONNECT'] === PermissionsAndroid.RESULTS.GRANTED
          );
        } else {
          // Android 11 and below
          const granted = await PermissionsAndroid.request(
            PermissionsAndroid.PERMISSIONS.ACCESS_FINE_LOCATION
          );
          return granted === PermissionsAndroid.RESULTS.GRANTED;
        }
      } catch (error) {
        console.error('Error requesting Bluetooth permissions:', error);
        return false;
      }
    }
    return true; // iOS doesn't require runtime permissions for Bluetooth
  }

  /**
   * Check if Bluetooth is enabled
   */
  async isBluetoothEnabled(): Promise<boolean> {
    try {
      return await BluetoothManager.isBluetoothEnabled();
    } catch (error) {
      console.error('Error checking Bluetooth status:', error);
      return false;
    }
  }

  /**
   * Enable Bluetooth
   */
  async enableBluetooth(): Promise<void> {
    try {
      await BluetoothManager.enableBluetooth();
    } catch (error) {
      console.error('Error enabling Bluetooth:', error);
      throw new Error('Failed to enable Bluetooth');
    }
  }

  /**
   * Discover available Bluetooth devices
   */
  async discoverDevices(): Promise<BluetoothDevice[]> {
    this.isDiscovering = true;

    try {
      const hasPermission = await this.requestPermissions();
      if (!hasPermission) {
        throw new Error('Bluetooth permissions not granted');
      }

      const isEnabled = await this.isBluetoothEnabled();
      if (!isEnabled) {
        throw new Error('Bluetooth is not enabled');
      }

      // Get paired devices
      const pairedDevices = await BluetoothManager.list();

      // Scan for unpaired devices
      const unpairedDevices = await BluetoothManager.scanDevices();

      const devices: BluetoothDevice[] = [
        ...pairedDevices.map((device: any) => ({
          id: device.address,
          name: device.name || 'Unknown Device',
          address: device.address,
          paired: true,
          connected: false,
        })),
        ...JSON.parse(unpairedDevices.found || '[]').map((device: any) => ({
          id: device.address,
          name: device.name || 'Unknown Device',
          address: device.address,
          paired: false,
          connected: false,
        })),
      ];

      return devices;
    } catch (error) {
      console.error('Error discovering devices:', error);
      throw error;
    } finally {
      this.isDiscovering = false;
    }
  }

  /**
   * Connect to a Bluetooth device
   */
  async connect(device: BluetoothDevice): Promise<void> {
    try {
      await BluetoothManager.connect(device.address);
      this.connectedDevice = { ...device, connected: true };
      console.log('Connected to printer:', device.name);
    } catch (error) {
      console.error('Error connecting to device:', error);
      throw new Error(`Failed to connect to ${device.name}`);
    }
  }

  /**
   * Disconnect from current device
   */
  async disconnect(): Promise<void> {
    try {
      if (this.connectedDevice) {
        await BluetoothManager.disconnect();
        this.connectedDevice = null;
        console.log('Disconnected from printer');
      }
    } catch (error) {
      console.error('Error disconnecting:', error);
      throw new Error('Failed to disconnect from printer');
    }
  }

  /**
   * Get connected device
   */
  getConnectedDevice(): BluetoothDevice | null {
    return this.connectedDevice;
  }

  /**
   * Check if connected
   */
  isConnected(): boolean {
    return this.connectedDevice !== null;
  }

  /**
   * Print raw ESC/POS commands
   */
  private async printRaw(commands: string): Promise<void> {
    if (!this.isConnected()) {
      throw new Error('No printer connected');
    }

    try {
      // Convert commands string to byte array if needed
      await BluetoothEscposPrinter.printerInit();
      await BluetoothEscposPrinter.printText(commands, {});
      console.log('Print successful');
    } catch (error) {
      console.error('Error printing:', error);
      throw new Error('Failed to print');
    }
  }

  /**
   * Print invoice
   */
  async printInvoice(data: InvoicePrintData, options?: PrintOptions): Promise<void> {
    try {
      const commands = EscPosBuilder.buildInvoice(data);
      await this.printRaw(commands);
    } catch (error) {
      console.error('Error printing invoice:', error);
      throw error;
    }
  }

  /**
   * Print receipt
   */
  async printReceipt(data: ReceiptPrintData, options?: PrintOptions): Promise<void> {
    try {
      const commands = EscPosBuilder.buildReceipt(data);
      await this.printRaw(commands);
    } catch (error) {
      console.error('Error printing receipt:', error);
      throw error;
    }
  }

  /**
   * Print job summary
   */
  async printJobSummary(data: JobSummaryPrintData, options?: PrintOptions): Promise<void> {
    try {
      const commands = EscPosBuilder.buildJobSummary(data);
      await this.printRaw(commands);
    } catch (error) {
      console.error('Error printing job summary:', error);
      throw error;
    }
  }

  /**
   * Generic print method based on print job type
   */
  async print(job: PrintJob, options?: PrintOptions): Promise<void> {
    switch (job.type) {
      case 'invoice':
        await this.printInvoice(job.data as InvoicePrintData, options);
        break;
      case 'receipt':
        await this.printReceipt(job.data as ReceiptPrintData, options);
        break;
      case 'job_summary':
        await this.printJobSummary(job.data as JobSummaryPrintData, options);
        break;
      default:
        throw new Error(`Unknown print job type: ${job.type}`);
    }
  }

  /**
   * Test print
   */
  async testPrint(): Promise<void> {
    if (!this.isConnected()) {
      throw new Error('No printer connected');
    }

    const builder = new EscPosBuilder();
    const commands = builder
      .align('center')
      .size('double')
      .bold(true)
      .textLn('TEST PRINT')
      .size('normal')
      .bold(false)
      .feed(1)
      .textLn('GPS Field Management')
      .textLn('Bluetooth Printer Test')
      .feed(1)
      .textLn(new Date().toLocaleString())
      .feed(3)
      .cut()
      .build();

    await this.printRaw(commands);
  }
}

// Export singleton instance
export const bluetoothPrinterService = new BluetoothPrinterService();
