import { PermissionsAndroid, Platform } from 'react-native';
import Geolocation from 'react-native-geolocation-service';

class GPSService {
    private watchId: number | null = null;

    constructor() {
        this.requestLocationPermission();
    }

    private async requestLocationPermission() {
        if (Platform.OS === 'android') {
            const granted = await PermissionsAndroid.request(
                PermissionsAndroid.PERMISSIONS.ACCESS_FINE_LOCATION,
                {
                    title: 'Location Permission',
                    message: 'This app needs access to your location.',
                    buttonNeutral: 'Ask Me Later',
                    buttonNegative: 'Cancel',
                    buttonPositive: 'OK',
                },
            );
            return granted === PermissionsAndroid.RESULTS.GRANTED;
        }
        return true; // iOS automatically grants permission
    }

    public getCurrentLocation(): Promise<Geolocation.GeoPosition> {
        return new Promise((resolve, reject) => {
            Geolocation.getCurrentPosition(
                (position) => {
                    resolve(position);
                },
                (error) => {
                    reject(error);
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 10000 },
            );
        });
    }

    public startTracking(callback: (position: Geolocation.GeoPosition) => void) {
        this.watchId = Geolocation.watchPosition(
            (position) => {
                callback(position);
            },
            (error) => {
                console.error(error);
            },
            { enableHighAccuracy: true, distanceFilter: 0, interval: 5000, fastestInterval: 2000 },
        );
    }

    public stopTracking() {
        if (this.watchId !== null) {
            Geolocation.clearWatch(this.watchId);
            this.watchId = null;
        }
    }
}

export default new GPSService();