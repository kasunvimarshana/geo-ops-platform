/**
 * API services for the application
 */

export * from './api';
export * from './authService';
export * from './fieldService';

// Export all API services
export { default as authApi } from './api/auth';
export { default as measurementApi } from './api/measurements';
export { default as jobApi } from './api/jobs';
export { default as invoiceApi } from './api/invoices';
export { default as paymentApi } from './api/payments';
export { default as expenseApi } from './api/expenses';
export { default as reportApi } from './api/reports';
export { customerApi } from './api/customers';
export { driverApi } from './api/drivers';
export { machineApi } from './api/machines';
export { trackingApi } from './api/tracking';
export { syncApi } from './api/sync';

// Export types
export type { LoginData, RegisterData, AuthResponse } from './api/auth';
export type { Measurement, CreateMeasurementData } from './api/measurements';
export type { Job, CreateJobData, UpdateJobStatusData, AssignJobData } from './api/jobs';
export type { Invoice, CreateInvoiceData, GenerateInvoiceFromJobData } from './api/invoices';
export type { Payment, CreatePaymentData } from './api/payments';
export type { Expense, CreateExpenseData } from './api/expenses';
export type { Customer, CustomerStatistics } from './api/customers';
export type { Driver, DriverStatistics } from './api/drivers';
export type { Machine, MachineStatistics } from './api/machines';
export type { TrackingLog, TrackingLogBatch, ActiveDriver } from './api/tracking';
export type { SyncPushData, SyncPushResult, SyncPullData } from './api/sync';
