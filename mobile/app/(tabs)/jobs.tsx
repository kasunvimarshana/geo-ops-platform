import React, { useEffect } from 'react';
import { View, Text, FlatList, StyleSheet } from 'react-native';
import { useJobs } from '../../hooks/useJobs';
import JobCard from '../components/jobs/JobCard';
import Loading from '../components/common/Loading';

const JobsScreen = () => {
  const { jobs, loading, fetchJobs } = useJobs();

  useEffect(() => {
    fetchJobs();
  }, []);

  if (loading) {
    return <Loading />;
  }

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Jobs</Text>
      <FlatList
        data={jobs}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item }) => <JobCard job={item} />}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 16,
    backgroundColor: '#fff',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 16,
  },
});

export default JobsScreen;