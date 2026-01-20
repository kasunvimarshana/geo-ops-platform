# Contributing to GPS Field Management Platform

Thank you for your interest in contributing to the GPS Field Management Platform! This document provides guidelines for contributing to the project.

## 🌟 How to Contribute

### Reporting Bugs

- Check if the bug has already been reported in [Issues](https://github.com/kasunvimarshana/geo-ops-platform/issues)
- Use the bug report template
- Include detailed steps to reproduce
- Provide system information (OS, PHP version, Node version, etc.)

### Suggesting Features

- Check existing feature requests
- Clearly describe the feature and use case
- Explain why it would be beneficial
- Consider implementation complexity

### Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Make your changes
4. Write/update tests
5. Update documentation
6. Commit with clear messages (`git commit -m 'Add AmazingFeature'`)
7. Push to your fork (`git push origin feature/AmazingFeature`)
8. Open a Pull Request

## 📝 Code Standards

### Backend (PHP/Laravel)

- Follow PSR-12 coding standards
- Use type hints for all parameters and return types
- Write PHPDoc blocks for all methods
- Keep controllers thin (business logic in services)
- Use repository pattern for data access
- Write unit tests for services
- Run `composer pint` before committing

### Mobile (TypeScript/React Native)

- Use TypeScript strict mode
- Follow Airbnb JavaScript/React style guide
- Use functional components with hooks
- Keep components small and focused
- Write meaningful variable names
- Add PropTypes or TypeScript interfaces
- Run `npm run lint` before committing

### General

- Write clear, concise commit messages
- One feature/fix per pull request
- Update documentation as needed
- Add tests for new features
- Ensure all tests pass
- Keep PRs reasonably sized

## 🧪 Testing

### Backend Tests

```bash
cd backend
php artisan test
```

### Mobile Tests

```bash
cd mobile
npm test
```

## 📚 Documentation

- Update README.md if adding major features
- Update API_DOCUMENTATION.md for API changes
- Add inline comments for complex logic
- Update CHANGELOG.md

## 🔄 Development Workflow

1. **Setup**: Follow QUICK_START.md
2. **Branch**: Create from `main` or `develop`
3. **Code**: Make your changes
4. **Test**: Run all tests
5. **Document**: Update docs
6. **Commit**: Use clear messages
7. **Push**: Push to your fork
8. **PR**: Open a pull request

## ✅ Pull Request Checklist

- [ ] Code follows project standards
- [ ] All tests pass
- [ ] New tests added for new features
- [ ] Documentation updated
- [ ] Commit messages are clear
- [ ] No merge conflicts
- [ ] PR description explains changes

## 🏗️ Project Structure

```
geo-ops-platform/
├── backend/        # Laravel API
├── mobile/         # React Native App
├── docs/           # Documentation
└── README.md       # Project overview
```

## 🤝 Code Review Process

1. Maintainers review PRs
2. Feedback provided via comments
3. Address requested changes
4. Approved PRs are merged
5. Celebrate! 🎉

## 📧 Contact

- GitHub Issues: [Report Issues](https://github.com/kasunvimarshana/geo-ops-platform/issues)
- Discussions: [GitHub Discussions](https://github.com/kasunvimarshana/geo-ops-platform/discussions)

## 📄 License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

**Thank you for contributing to GPS Field Management Platform!** 🚀
