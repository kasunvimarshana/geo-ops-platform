# Security Policy

## Reporting a Vulnerability

We take the security of GeoOps Platform seriously. If you discover a security vulnerability, please report it responsibly.

### How to Report

**DO NOT** open a public GitHub issue for security vulnerabilities.

Instead, please report security issues via email to: **kasunvmail@gmail.com**

Include the following information:

- Type of vulnerability
- Affected components/versions
- Steps to reproduce
- Potential impact
- Suggested fix (if available)

We will acknowledge receipt within 48 hours and provide a detailed response within 7 days.

## Security Measures

### Backend Security

#### Authentication & Authorization

- ✅ JWT-based authentication with refresh tokens
- ✅ Role-Based Access Control (RBAC)
- ✅ Password hashing with bcrypt
- ✅ Rate limiting on authentication endpoints (5 requests/minute)
- ✅ Token expiration and refresh mechanism

#### API Security

- ✅ CORS configuration with environment-based origins
- ✅ Input validation using Form Request classes
- ✅ SQL injection protection via Eloquent ORM
- ✅ XSS protection through output escaping
- ✅ CSRF protection for web routes
- ✅ HTTPS enforcement (production)

#### Data Protection

- ✅ Encrypted database connections
- ✅ Organization-based data isolation
- ✅ Sensitive data sanitization in logs
- ✅ Regular security audits

### Mobile Security

#### Data Storage

- ✅ Encrypted local storage (MMKV)
- ✅ Secure token storage
- ✅ Auto-generated encryption keys with device-specific fallback

#### Network Security

- ✅ HTTPS-only API communication
- ✅ Certificate pinning (recommended for production)
- ✅ Token-based authentication
- ✅ Automatic token refresh

#### Code Security

- ✅ Global error boundary
- ✅ Input validation
- ✅ No hardcoded secrets
- ✅ ProGuard/R8 obfuscation (Android)

## Security Best Practices

### For Developers

1. **Never commit secrets**
   - Use `.env` files (never commit these)
   - Use environment variables for sensitive data
   - Use secret management services in production

2. **Keep dependencies updated**
   - Regularly update composer packages
   - Regularly update npm packages
   - Monitor security advisories

3. **Code review**
   - All changes must be reviewed
   - Security-focused reviews for auth/payment code
   - Use automated security scanning

4. **Testing**
   - Write security tests
   - Test authentication flows
   - Test authorization boundaries
   - Test input validation

### For Deployment

1. **Environment Configuration**

   ```bash
   # Backend .env
   APP_ENV=production
   APP_DEBUG=false

   # Use strong passwords
   DB_PASSWORD=$(openssl rand -base64 32)
   JWT_SECRET=$(openssl rand -base64 64)

   # Restrict CORS
   CORS_ALLOWED_ORIGINS=https://your-domain.com

   # Enable rate limiting
   RATE_LIMIT_PER_MINUTE=60
   AUTH_RATE_LIMIT_PER_MINUTE=5
   ```

2. **SSL/TLS**
   - Use Let's Encrypt for free SSL certificates
   - Enable HTTPS redirect
   - Use HSTS headers
   - TLS 1.2 minimum

3. **Server Hardening**
   - Keep OS and packages updated
   - Configure firewall (UFW)
   - Disable unnecessary services
   - Use fail2ban for brute force protection
   - Regular backups

4. **Database Security**
   - Use strong passwords
   - Limit database user permissions
   - Enable audit logging
   - Regular backups
   - Encrypt backups

5. **Monitoring**
   - Set up error tracking (Sentry)
   - Monitor failed login attempts
   - Log security events
   - Set up alerts for suspicious activity

## Security Checklist

### Before Production Deployment

- [ ] All environment variables configured
- [ ] Debug mode disabled
- [ ] Strong passwords generated
- [ ] CORS properly configured
- [ ] Rate limiting enabled
- [ ] SSL certificate installed
- [ ] Firewall configured
- [ ] Database access restricted
- [ ] Error tracking configured
- [ ] Backups configured
- [ ] Security audit completed
- [ ] Dependency audit passed
- [ ] Mobile app code obfuscated
- [ ] API endpoints tested for vulnerabilities
- [ ] Authentication flows tested

### Regular Maintenance

- [ ] Weekly: Review access logs
- [ ] Weekly: Check failed login attempts
- [ ] Monthly: Update dependencies
- [ ] Monthly: Review user permissions
- [ ] Quarterly: Security audit
- [ ] Quarterly: Penetration testing
- [ ] Yearly: SSL certificate renewal (if not auto-renewed)

## Known Security Considerations

### Current Limitations

1. **Mobile App**
   - Certificate pinning not implemented (recommended for v2)
   - Biometric authentication not implemented
   - Offline data not encrypted (uses MMKV encryption)

2. **Backend**
   - No WAF (Web Application Firewall) - recommended for high-traffic
   - No DDoS protection - use Cloudflare or similar
   - Email verification not mandatory

3. **Infrastructure**
   - Redis not password-protected in default Docker setup
   - Database connections not encrypted by default

### Roadmap

- [ ] Implement certificate pinning
- [ ] Add biometric authentication
- [ ] Add email verification
- [ ] Implement 2FA (Two-Factor Authentication)
- [ ] Add IP whitelisting for admin endpoints
- [ ] Implement audit logging
- [ ] Add intrusion detection

## Compliance

This application follows these security standards:

- OWASP Top 10 protection
- GDPR compliance considerations
- Data encryption at rest and in transit
- Regular security updates

## Security Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security](https://laravel.com/docs/security)
- [React Native Security](https://reactnative.dev/docs/security)
- [NIST Cybersecurity Framework](https://www.nist.gov/cyberframework)

## Contact

For security inquiries: kasunvmail@gmail.com
For general support: kasunvmail@gmail.com

---

**Last Updated:** 2026-01-19
