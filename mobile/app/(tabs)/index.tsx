import { View, Text, StyleSheet, ScrollView } from 'react-native';
import { useAuthStore } from '../../src/store/auth.store';

export default function HomeTab() {
  const user = useAuthStore((state) => state.user);

  return (
    <ScrollView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.greeting}>Welcome back,</Text>
        <Text style={styles.name}>{user?.firstName} {user?.lastName}</Text>
      </View>

      <View style={styles.card}>
        <Text style={styles.cardTitle}>Quick Stats</Text>
        <View style={styles.statsContainer}>
          <View style={styles.stat}>
            <Text style={styles.statValue}>0</Text>
            <Text style={styles.statLabel}>Measurements</Text>
          </View>
          <View style={styles.stat}>
            <Text style={styles.statValue}>0</Text>
            <Text style={styles.statLabel}>Jobs</Text>
          </View>
          <View style={styles.stat}>
            <Text style={styles.statValue}>0</Text>
            <Text style={styles.statLabel}>Invoices</Text>
          </View>
        </View>
      </View>

      <View style={styles.card}>
        <Text style={styles.cardTitle}>Recent Activity</Text>
        <Text style={styles.emptyText}>No recent activity</Text>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F5F5F5',
  },
  header: {
    backgroundColor: '#2196F3',
    padding: 20,
    paddingTop: 40,
  },
  greeting: {
    color: '#fff',
    fontSize: 16,
  },
  name: {
    color: '#fff',
    fontSize: 24,
    fontWeight: 'bold',
    marginTop: 5,
  },
  card: {
    backgroundColor: '#fff',
    margin: 15,
    padding: 20,
    borderRadius: 10,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
  },
  cardTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 15,
    color: '#212121',
  },
  statsContainer: {
    flexDirection: 'row',
    justifyContent: 'space-around',
  },
  stat: {
    alignItems: 'center',
  },
  statValue: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#2196F3',
  },
  statLabel: {
    fontSize: 14,
    color: '#757575',
    marginTop: 5,
  },
  emptyText: {
    textAlign: 'center',
    color: '#757575',
    paddingVertical: 20,
  },
});
