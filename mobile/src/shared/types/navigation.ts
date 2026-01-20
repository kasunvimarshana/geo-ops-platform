import type { NavigatorScreenParams } from '@react-navigation/native';

export type RootStackParamList = {
  Auth: NavigatorScreenParams<AuthStackParamList>;
  Main: NavigatorScreenParams<MainTabParamList>;
};

export type AuthStackParamList = {
  Login: undefined;
  Register: undefined;
  ForgotPassword: undefined;
};

export type MainTabParamList = {
  Home: undefined;
  Lands: undefined;
  Jobs: undefined;
  Financial: undefined;
  Profile: undefined;
};

export type LandsStackParamList = {
  LandsList: undefined;
  LandDetails: { landId: string };
  CreateLand: undefined;
  Measurement: { landId?: string };
};

export type JobsStackParamList = {
  JobsList: undefined;
  JobDetails: { jobId: string };
  CreateJob: undefined;
};

export type FinancialStackParamList = {
  FinancialOverview: undefined;
  Invoices: undefined;
  InvoiceDetails: { invoiceId: string };
  Payments: undefined;
  Expenses: undefined;
  CreateExpense: undefined;
};

declare global {
  namespace ReactNavigation {
    interface RootParamList extends RootStackParamList {}
  }
}
