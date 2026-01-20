import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createStackNavigator } from '@react-navigation/stack';
import { JobListScreen } from '../features/jobs/screens/JobListScreen';
import { CreateJobScreen } from '../features/jobs/screens/CreateJobScreen';
import { JobDetailScreen } from '../features/jobs/screens/JobDetailScreen';
import { MeasurementScreen } from '../features/gps/screens/MeasurementScreen';
import { PrinterSettingsScreen, PrintQueueScreen } from '../features/printer';
import { colors } from '../theme/colors';

export type MainTabParamList = {
  JobsTab: undefined;
  GPSTab: undefined;
  PrinterTab: undefined;
};

export type JobsStackParamList = {
  JobList: undefined;
  CreateJob: undefined;
  JobDetail: { jobId: number };
};

export type PrinterStackParamList = {
  PrinterSettings: undefined;
  PrintQueue: undefined;
};

const Tab = createBottomTabNavigator<MainTabParamList>();
const JobsStack = createStackNavigator<JobsStackParamList>();
const PrinterStack = createStackNavigator<PrinterStackParamList>();

const JobsNavigator = () => {
  return (
    <JobsStack.Navigator>
      <JobsStack.Screen
        name="JobList"
        component={JobListScreen}
        options={{ headerShown: false }}
      />
      <JobsStack.Screen
        name="CreateJob"
        component={CreateJobScreen}
        options={{
          title: 'Create Job',
          headerStyle: { backgroundColor: colors.primary },
          headerTintColor: colors.text.white,
        }}
      />
      <JobsStack.Screen
        name="JobDetail"
        component={JobDetailScreen}
        options={{
          title: 'Job Details',
          headerStyle: { backgroundColor: colors.primary },
          headerTintColor: colors.text.white,
        }}
      />
    </JobsStack.Navigator>
  );
};

const PrinterNavigator = () => {
  return (
    <PrinterStack.Navigator>
      <PrinterStack.Screen
        name="PrinterSettings"
        component={PrinterSettingsScreen}
        options={{
          title: 'Printer Settings',
          headerStyle: { backgroundColor: colors.primary },
          headerTintColor: colors.text.white,
        }}
      />
      <PrinterStack.Screen
        name="PrintQueue"
        component={PrintQueueScreen}
        options={{
          title: 'Print Queue',
          headerStyle: { backgroundColor: colors.primary },
          headerTintColor: colors.text.white,
        }}
      />
    </PrinterStack.Navigator>
  );
};

export const MainNavigator: React.FC = () => {
  return (
    <Tab.Navigator
      screenOptions={{
        tabBarActiveTintColor: colors.primary,
        tabBarInactiveTintColor: colors.text.disabled,
        headerShown: false,
      }}
    >
      <Tab.Screen
        name="JobsTab"
        component={JobsNavigator}
        options={{
          title: 'Jobs',
          tabBarIcon: ({ color, size }) => null, // Add icon if needed
        }}
      />
      <Tab.Screen
        name="GPSTab"
        component={MeasurementScreen}
        options={{
          title: 'GPS',
          tabBarIcon: ({ color, size }) => null, // Add icon if needed
        }}
      />
      <Tab.Screen
        name="PrinterTab"
        component={PrinterNavigator}
        options={{
          title: 'Printer',
          tabBarIcon: ({ color, size }) => null, // Add icon if needed
        }}
      />
    </Tab.Navigator>
  );
};
