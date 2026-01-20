import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import type { MainTabParamList } from '../shared/types/navigation';
import { colors } from '../theme';

const HomeScreen = () => null;
const LandsScreen = () => null;
const JobsScreen = () => null;
const FinancialScreen = () => null;
const ProfileScreen = () => null;

const Tab = createBottomTabNavigator<MainTabParamList>();

export const MainTabs = () => {
  return (
    <Tab.Navigator
      screenOptions={{
        headerShown: true,
        tabBarActiveTintColor: colors.primary,
        tabBarInactiveTintColor: colors.textSecondary,
        tabBarStyle: {
          backgroundColor: colors.surface,
          borderTopColor: colors.border,
        },
      }}
    >
      <Tab.Screen 
        name="Home" 
        component={HomeScreen}
        options={{ title: 'Home' }}
      />
      <Tab.Screen 
        name="Lands" 
        component={LandsScreen}
        options={{ title: 'Lands' }}
      />
      <Tab.Screen 
        name="Jobs" 
        component={JobsScreen}
        options={{ title: 'Jobs' }}
      />
      <Tab.Screen 
        name="Financial" 
        component={FinancialScreen}
        options={{ title: 'Financial' }}
      />
      <Tab.Screen 
        name="Profile" 
        component={ProfileScreen}
        options={{ title: 'Profile' }}
      />
    </Tab.Navigator>
  );
};
