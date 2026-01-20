# Known Issues and Notes

## Area Calculation

### Current Implementation
The land measurement area calculation uses a spherical excess formula which provides reasonable accuracy for small to medium-sized plots. This is suitable for agricultural land measurement in most cases.

### Accuracy Notes
- **Small plots (< 1 hectare)**: Accuracy within 1-2%
- **Medium plots (1-10 hectares)**: Accuracy within 2-5%
- **Large plots (> 10 hectares)**: May require more sophisticated geodetic calculations

### Recommendations for Production
For higher accuracy in production deployments, consider:

1. **Use Turf.js**: Industry-standard library for geospatial calculations
   ```bash
   npm install @turf/turf
   ```

2. **PostGIS Extension**: For server-side calculations
   ```sql
   CREATE EXTENSION postgis;
   ```

3. **Alternative Libraries**:
   - `geolib` - Lightweight geospatial calculations
   - `proj4js` - Coordinate transformations
   - `gdal` - Advanced GIS operations

### Example with Turf.js

```typescript
import * as turf from '@turf/turf';

function calculateAreaWithTurf(coordinates: GpsCoordinate[]): number {
  const polygon = turf.polygon([[
    ...coordinates.map(c => [c.longitude, c.latitude]),
    [coordinates[0].longitude, coordinates[0].latitude] // Close the polygon
  ]]);
  
  const area = turf.area(polygon); // Returns area in square meters
  return area;
}
```

## Future Improvements

### High Priority
- [ ] Integrate PostGIS for server-side geospatial calculations
- [ ] Add turf.js for client-side area calculations
- [ ] Validate GPS coordinate accuracy and filtering
- [ ] Add polygon validation (no self-intersections)

### Medium Priority
- [ ] Support for elevation data
- [ ] Terrain-adjusted area calculations
- [ ] Multi-polygon support
- [ ] Coordinate system transformations

## GPS Accuracy Factors

The accuracy of land measurements depends on several factors:

1. **GPS Signal Quality**
   - Clear sky view: ±5m accuracy
   - Partial obstruction: ±10-20m accuracy
   - Heavy obstruction: ±50m+ accuracy

2. **Device Capabilities**
   - High-end phones: 3-5m accuracy
   - Mid-range phones: 5-10m accuracy
   - Budget phones: 10-20m accuracy

3. **Measurement Technique**
   - Walk slowly around perimeter
   - Take points at regular intervals (every 5-10m)
   - Avoid tall buildings and tree cover
   - Measure during good weather

## Testing Recommendations

### Unit Tests Needed
- [ ] Area calculation with known coordinates
- [ ] Edge cases (3 points, collinear points)
- [ ] Large polygons
- [ ] Different coordinate systems

### Integration Tests Needed
- [ ] End-to-end measurement flow
- [ ] Offline to online sync
- [ ] Concurrent measurements
- [ ] Data integrity checks

## Security Considerations

### Implemented
- ✅ JWT authentication
- ✅ Password hashing
- ✅ Input validation
- ✅ SQL injection protection
- ✅ CORS protection
- ✅ Rate limiting
- ✅ **Multer upgraded to v2.0.2** (fixes DoS vulnerabilities)

### Vulnerability Fixes
- ✅ **Multer v2.0.2**: Fixed multiple DoS vulnerabilities
  - CVE: Denial of Service via unhandled exception from malformed request
  - CVE: Denial of Service via unhandled exception
  - CVE: Denial of Service from maliciously crafted requests
  - CVE: Denial of Service via memory leaks from unclosed streams
  - Previous version: 1.4.5-lts.1 (vulnerable)
  - Current version: 2.0.2 (patched)

### Recommended for Production
- [ ] API key rotation
- [ ] Audit logging
- [ ] Data encryption at rest
- [ ] HTTPS enforcement
- [ ] IP whitelisting for admin
- [ ] Two-factor authentication

## Performance Considerations

### Current Optimizations
- Database indexes on foreign keys
- Connection pooling
- Pagination on list endpoints
- JSONB for flexible data

### Recommended Improvements
- [ ] Redis caching for frequently accessed data
- [ ] CDN for static assets
- [ ] Database query optimization
- [ ] API response compression
- [ ] Background job queue for heavy operations

## Browser/Platform Compatibility

### Tested Platforms
- ✅ Node.js 18+ (Backend)
- ✅ Expo Go (Mobile development)
- ✅ PostgreSQL 15+ (Database)

### Recommended Testing
- [ ] iOS 14+ physical devices
- [ ] Android 10+ physical devices
- [ ] Various screen sizes
- [ ] Low-end devices
- [ ] Slow network conditions

## Deployment Checklist

### Before Production
- [ ] Change JWT secret
- [ ] Update CORS origins
- [ ] Configure SSL/TLS
- [ ] Set up monitoring
- [ ] Configure backups
- [ ] Load testing
- [ ] Security audit
- [ ] Update documentation

### Environment Variables
Ensure all sensitive data is in environment variables:
- `JWT_SECRET` - Strong random string
- `DB_PASSWORD` - Strong database password
- `API_KEYS` - Third-party service keys

## Known Limitations

1. **Offline Sync**: Not fully implemented - SQLite integration needed
2. **Real-time Tracking**: WebSocket infrastructure not set up
3. **PDF Generation**: Invoice PDF export pending
4. **Multi-language**: Currently only English
5. **Payment Integration**: No payment gateway integration yet

## Contributing Guidelines

When contributing, please:
- Follow existing code style
- Add tests for new features
- Update documentation
- Use TypeScript strictly
- Follow SOLID principles
- Add comments for complex logic

## Support Resources

- GitHub Issues: For bug reports
- Documentation: Check README files
- Architecture: See ARCHITECTURE.md
- API Reference: See API_REFERENCE.md
