import { Notifications } from 'expo-notifications';

class NotificationService {
    async scheduleNotification(title: string, body: string, data?: object) {
        await Notifications.scheduleNotificationAsync({
            content: {
                title: title,
                body: body,
                data: data,
            },
            trigger: {
                seconds: 1, // Schedule for 1 second later for testing
            },
        });
    }

    async getNotificationPermissions() {
        const { status } = await Notifications.getPermissionsAsync();
        if (status !== 'granted') {
            const { status: newStatus } = await Notifications.requestPermissionsAsync();
            return newStatus === 'granted';
        }
        return true;
    }

    async registerForPushNotificationsAsync() {
        const hasPermission = await this.getNotificationPermissions();
        if (!hasPermission) return;

        const token = (await Notifications.getExpoPushTokenAsync()).data;
        return token;
    }

    // Add more notification-related methods as needed
}

export default new NotificationService();