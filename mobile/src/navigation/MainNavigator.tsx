import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createStackNavigator } from '@react-navigation/stack';
import { Text, View, StyleSheet } from 'react-native';
import { MeasurementListScreen, MeasurementScreen } from '@/features/measurement';
import { MapScreen } from '@/features/maps';
import { JobListScreen } from '@/features/jobs';
import { InvoiceListScreen } from '@/features/billing';
import { ProfileScreen } from '@/features/auth';
import { SyncScreen } from '@/features/sync';

const Tab = createBottomTabNavigator();
const Stack = createStackNavigator();

const TabIcon: React.FC<{ icon: string; focused: boolean }> = ({ icon, focused }) => (
  <Text style={[styles.tabIcon, focused && styles.tabIconFocused]}>{icon}</Text>
);

const MeasurementStack = () => (
  <Stack.Navigator screenOptions={{ headerShown: false }}>
    <Stack.Screen name="MeasurementList" component={MeasurementListScreen} />
    <Stack.Screen name="Measurement" component={MeasurementScreen} />
  </Stack.Navigator>
);

const JobStack = () => (
  <Stack.Navigator screenOptions={{ headerShown: false }}>
    <Stack.Screen name="JobList" component={JobListScreen} />
  </Stack.Navigator>
);

const BillingStack = () => (
  <Stack.Navigator screenOptions={{ headerShown: false }}>
    <Stack.Screen name="InvoiceList" component={InvoiceListScreen} />
  </Stack.Navigator>
);

const ProfileStack = () => (
  <Stack.Navigator screenOptions={{ headerShown: false }}>
    <Stack.Screen name="ProfileMain" component={ProfileScreen} />
    <Stack.Screen name="Sync" component={SyncScreen} />
  </Stack.Navigator>
);

export const MainNavigator: React.FC = () => {
  return (
    <Tab.Navigator
      screenOptions={{
        headerShown: false,
        tabBarActiveTintColor: '#2196F3',
        tabBarInactiveTintColor: '#999',
        tabBarStyle: styles.tabBar,
        tabBarLabelStyle: styles.tabLabel,
      }}
    >
      <Tab.Screen
        name="MeasurementTab"
        component={MeasurementStack}
        options={{
          tabBarLabel: 'Measure',
          tabBarIcon: ({ focused }) => <TabIcon icon="📐" focused={focused} />,
        }}
      />
      <Tab.Screen
        name="MapTab"
        component={MapScreen}
        options={{
          tabBarLabel: 'Map',
          tabBarIcon: ({ focused }) => <TabIcon icon="🗺️" focused={focused} />,
        }}
      />
      <Tab.Screen
        name="JobTab"
        component={JobStack}
        options={{
          tabBarLabel: 'Jobs',
          tabBarIcon: ({ focused }) => <TabIcon icon="🚜" focused={focused} />,
        }}
      />
      <Tab.Screen
        name="BillingTab"
        component={BillingStack}
        options={{
          tabBarLabel: 'Billing',
          tabBarIcon: ({ focused }) => <TabIcon icon="💰" focused={focused} />,
        }}
      />
      <Tab.Screen
        name="ProfileTab"
        component={ProfileStack}
        options={{
          tabBarLabel: 'Profile',
          tabBarIcon: ({ focused }) => <TabIcon icon="👤" focused={focused} />,
        }}
      />
    </Tab.Navigator>
  );
};

const styles = StyleSheet.create({
  tabBar: {
    borderTopWidth: 1,
    borderTopColor: '#E0E0E0',
    paddingTop: 8,
    paddingBottom: 8,
    height: 64,
  },
  tabLabel: {
    fontSize: 12,
    fontWeight: '600',
  },
  tabIcon: {
    fontSize: 24,
  },
  tabIconFocused: {
    transform: [{ scale: 1.1 }],
  },
});
