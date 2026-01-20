import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import Dashboard from './dashboard';
import Jobs from './jobs';
import Lands from './lands';
import Profile from './profile';
import { NavigationContainer } from '@react-navigation/native';
import { Icon } from 'react-native-elements';

const Tab = createBottomTabNavigator();

const Layout = () => {
  return (
    <NavigationContainer>
      <Tab.Navigator
        screenOptions={({ route }) => ({
          tabBarIcon: ({ focused, color, size }) => {
            let iconName;

            if (route.name === 'Dashboard') {
              iconName = focused ? 'dashboard' : 'dashboard-outline';
            } else if (route.name === 'Jobs') {
              iconName = focused ? 'work' : 'work-outline';
            } else if (route.name === 'Lands') {
              iconName = focused ? 'map' : 'map-outline';
            } else if (route.name === 'Profile') {
              iconName = focused ? 'person' : 'person-outline';
            }

            return <Icon name={iconName} size={size} color={color} />;
          },
        })}
      >
        <Tab.Screen name="Dashboard" component={Dashboard} />
        <Tab.Screen name="Jobs" component={Jobs} />
        <Tab.Screen name="Lands" component={Lands} />
        <Tab.Screen name="Profile" component={Profile} />
      </Tab.Navigator>
    </NavigationContainer>
  );
};

export default Layout;