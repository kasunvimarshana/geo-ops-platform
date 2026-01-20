import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, StyleSheet } from 'react-native';
import { fetchLands } from '../../src/api/lands';
import LandCard from '../../src/components/lands/LandCard';
import Loading from '../../src/components/common/Loading';

const LandsScreen = () => {
  const [lands, setLands] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const loadLands = async () => {
      try {
        const data = await fetchLands();
        setLands(data);
      } catch (error) {
        console.error('Error fetching lands:', error);
      } finally {
        setLoading(false);
      }
    };

    loadLands();
  }, []);

  if (loading) {
    return <Loading />;
  }

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Lands</Text>
      <FlatList
        data={lands}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item }) => <LandCard land={item} />}
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

export default LandsScreen;