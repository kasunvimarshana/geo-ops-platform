import { Tabs } from 'expo-router';
import React from 'react';

export default function TabLayout() {
  return (
    <Tabs
      screenOptions={{
        headerStyle: {
          backgroundColor: '#2e7d32',
        },
        headerTintColor: '#fff',
        tabBarActiveTintColor: '#2e7d32',
        tabBarInactiveTintColor: '#999',
      }}
    >
      <Tabs.Screen
        name="index"
        options={{
          title: 'Dashboard',
          tabBarLabel: 'Home',
        }}
      />
      <Tabs.Screen
        name="measurements"
        options={{
          title: 'Measurements',
          tabBarLabel: 'Fields',
        }}
      />
      <Tabs.Screen
        name="jobs"
        options={{
          title: 'Jobs',
          tabBarLabel: 'Jobs',
        }}
      />
      <Tabs.Screen
        name="profile"
        options={{
          title: 'Profile',
          tabBarLabel: 'Profile',
        }}
      />
    </Tabs>
  );
}
