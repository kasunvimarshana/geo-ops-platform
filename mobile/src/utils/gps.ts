import * as Location from 'expo-location';

export const requestLocationPermission = async () => {
    const { status } = await Location.requestForegroundPermissionsAsync();
    if (status !== 'granted') {
        console.error('Permission to access location was denied');
        return false;
    }
    return true;
};

export const getCurrentLocation = async () => {
    const hasPermission = await requestLocationPermission();
    if (!hasPermission) return null;

    const location = await Location.getCurrentPositionAsync({});
    return location.coords;
};

export const watchLocation = (callback) => {
    const subscription = Location.watchPositionAsync(
        {
            accuracy: Location.Accuracy.High,
            timeInterval: 1000,
            distanceInterval: 1,
        },
        (location) => {
            callback(location.coords);
        }
    );

    return subscription;
};

export const stopWatchingLocation = (subscription) => {
    if (subscription) {
        subscription.remove();
    }
};