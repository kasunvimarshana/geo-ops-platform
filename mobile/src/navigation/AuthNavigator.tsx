import React from 'react';
import { createStackNavigator } from '@react-navigation/stack';
import LoginScreen from '../app/(auth)/login';
import RegisterScreen from '../app/(auth)/register';

const Stack = createStackNavigator();

const AuthNavigator = () => {
  return (
    <Stack.Navigator>
      <Stack.Screen 
        name="Login" 
        component={LoginScreen} 
        options={{ headerShown: false }} 
      />
      <Stack.Screen 
        name="Register" 
        component={RegisterScreen} 
        options={{ headerShown: false }} 
      />
    </Stack.Navigator>
  );
};

export default AuthNavigator;