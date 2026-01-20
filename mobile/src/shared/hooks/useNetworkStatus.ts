import { useState, useEffect } from 'react';
import NetInfo from '@react-native-community/netinfo';
import { NetworkStatus } from '../types/common.types';

export const useNetworkStatus = () => {
  const [networkStatus, setNetworkStatus] = useState<NetworkStatus>('unknown');
  const [isConnected, setIsConnected] = useState<boolean>(false);

  useEffect(() => {
    const unsubscribe = NetInfo.addEventListener((state) => {
      const connected = state.isConnected ?? false;
      setIsConnected(connected);
      setNetworkStatus(connected ? 'online' : 'offline');
    });

    return () => unsubscribe();
  }, []);

  return { networkStatus, isConnected };
};
