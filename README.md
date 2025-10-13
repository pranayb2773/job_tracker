# 📋 Job Application Tracker

> A powerful Laravel 12 application for tracking job applications with AI-powered CV analysis, built with modern tools and best practices.

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat&logo=php)](https://php.net)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=flat)](https://livewire.laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## ✨ Features

### 📊 Job Application Management
- Track applications through complete hiring lifecycle
- Status workflow: Applied → Screening → Interview → Offer → Accepted/Rejected
- Priority levels and tagging system
- Advanced filtering and search
- Bulk operations support

### 📄 Document Management
- PDF document upload and storage (user-specific folders)
- Multiple document types: CV, Cover Letter, Portfolio, etc.
- File size formatting and metadata tracking
- Secure file storage with hash-based integrity
- Download and delete operations

### 🤖 AI-Powered CV Analysis
- **Comprehensive ATS Scoring** (0-100 scale)
- **8 Scoring Dimensions**:
  - Metadata & Contact Information
  - Presentation & Formatting
  - Section Organisation
  - Content Quality
  - Keyword & Skill Relevance
  - Grammar & Spelling
  - Length & Brevity
  - Extra Sections
- **Smart Recommendations**: Top 3 actionable improvements
- **Section-by-Section Analysis**: Detailed feedback on each CV section
- **Database Caching**: Store analysis results for instant retrieval
- **Cost Optimization**: ~99% reduction in API costs through caching

### 🔐 Authentication & Security
- Laravel Fortify authentication with 2FA support
- Email verification
- Password reset functionality
- Session management
- User-specific data isolation

### 🎨 Modern UI/UX
- Flux Pro UI components
- Tailwind CSS 4.x styling
- Dark mode support throughout
- Responsive design
- Real-time updates with Livewire
- Toast notifications
- Confirmation modals

## 🚀 Quick Start

### Prerequisites

- **PHP** >= 8.3 with required extensions
- **Composer** >= 2.6
- **Node.js** >= 20 and npm >= 10
- **Database**: SQLite (quickstart) or MySQL/PostgreSQL

### Installation

```bash
# Clone and install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database (SQLite quickstart)
mkdir -p database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Build assets and start development
composer run dev
```

The application will be available at `http://127.0.0.1:8000`

## 🔧 Configuration

### Environment Variables

#### Application
```env
APP_NAME="Job Tracker"
APP_ENV=local
APP_KEY=base64:...
APP_URL=http://localhost:8000
```

#### Database
```env
DB_CONNECTION=sqlite
DB_DATABASE=./database/database.sqlite
```

#### AI Analysis (Required for CV Analysis)
```env
# Choose one provider
PRISM_PROVIDER=anthropic
ANTHROPIC_API_KEY=your_anthropic_api_key

# OR
# PRISM_PROVIDER=openai
# OPENAI_API_KEY=your_openai_api_key

# OR
# PRISM_PROVIDER=gemini
# GEMINI_API_KEY=your_gemini_api_key
```

**Get API Keys:**
- Anthropic Claude: https://console.anthropic.com/
- OpenAI: https://platform.openai.com/api-keys
- Google Gemini: https://ai.google.dev/

#### Mail (Optional - for authentication emails)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## 📖 Usage

### Development

Run all services in parallel:
```bash
composer run dev
```

Or run individually:
```bash
php artisan serve              # Application server
php artisan queue:listen       # Queue worker
php artisan pail --timeout=0   # Log viewer
npm run dev                    # Vite dev server
```

### Code Quality

```bash
# Format PHP code (required before commits)
vendor/bin/pint --dirty

# Format Blade templates
npx prettier --write "resources/**/*.blade.php"

# Run tests
php artisan test

# Run specific tests
php artisan test --filter=documents
```

### Production Deployment

```bash
# Build assets
npm run build

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Setup queue worker (use Supervisor)
php artisan queue:work --tries=3
```

## 🏗️ Architecture

### Tech Stack

| Category | Technology |
|----------|-----------|
| **Backend** | Laravel 12, PHP 8.3+ |
| **Frontend** | Livewire 3, Volt, Flux Pro UI |
| **Styling** | Tailwind CSS 4.x |
| **Build** | Vite 7 |
| **Database** | SQLite / MySQL / PostgreSQL |
| **Testing** | Pest 4 |
| **AI** | Laravel Prism (Anthropic/OpenAI/Gemini) |
| **PDF** | smalot/pdfparser |
| **Authentication** | Laravel Fortify |

### Project Structure

```
job_tracker/
├── app/
│   ├── Enums/
│   │   ├── ApplicationPriority.php
│   │   ├── ApplicationStatus.php
│   │   └── DocumentType.php
│   ├── Livewire/
│   │   ├── Application/
│   │   │   └── ListJobApplications.php
│   │   └── Document/
│   │       ├── ListDocuments.php
│   │       └── AnalyzeDocument.php
│   └── Models/
│       ├── User.php
│       ├── JobApplication.php
│       └── Document.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── prompts/
│   │   └── cv-analysis.md          # AI analysis prompt
│   └── views/
│       ├── livewire/
│       │   ├── application/
│       │   └── document/
│       │       ├── list-documents.blade.php
│       │       ├── analyze-document.blade.php
│       │       ├── table.blade.php
│       │       └── filters.blade.php
│       └── components/
│           └── confirmation-modal.blade.php
├── routes/
│   ├── web.php
│   └── auth.php
├── tests/
│   ├── Feature/
│   └── Unit/
├── CLAUDE.md                       # Project instructions
├── SETUP_CV_ANALYSIS.md           # CV analysis setup guide
└── CV_ANALYSIS_UPDATES.md         # Recent updates documentation
```

### Key Components

#### Livewire Components
- **ListDocuments**: Document listing with upload, download, delete
- **AnalyzeDocument**: AI-powered CV analysis with caching
- **ListJobApplications**: Job application tracking and management

#### Enums
- **ApplicationStatus**: Job application status workflow
- **ApplicationPriority**: Priority levels (High, Medium, Low)
- **DocumentType**: Document categorization with icons and colors

#### Models
- **Document**: PDF documents with analysis caching
- **JobApplication**: Job applications with status tracking
- **User**: Authentication and relationships

## 🤖 AI CV Analysis

### Features
- Extracts text from PDF files
- Analyzes with Claude/GPT/Gemini
- Generates comprehensive ATS scores
- Provides actionable recommendations
- Caches results in database

### Customization

Edit the AI prompt at `resources/prompts/cv-analysis.md` to customize:
- Analysis criteria
- Scoring dimensions
- Recommendation types
- Output format

### Cost Considerations

| Provider | Cost per Analysis | Speed |
|----------|------------------|-------|
| **Claude 3.5 Sonnet** | ~$0.015 | Fast |
| **GPT-4** | ~$0.30 | Medium |
| **GPT-3.5** | ~$0.002 | Very Fast |
| **Gemini Pro** | ~$0.001 | Fast |

With database caching, these costs apply only once per document.

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test tests/Feature/DocumentTest.php

# Run with filter
php artisan test --filter=upload
```

### Test Conventions
- Feature tests in `tests/Feature/`
- Unit tests in `tests/Unit/`
- Use Pest syntax
- Leverage model factories
- Test Volt components with `Volt::test()`

## 📝 Code Style

This project follows strict PHP coding standards:

- `declare(strict_types=1)` on all PHP files
- All classes are `final` unless designed for extension
- Explicit return types on all methods
- Constructor property promotion
- Strict comparisons (`===`, `!==`)
- PSR-12 code style (enforced by Laravel Pint)

## 🐛 Troubleshooting

### Common Issues

**Vite assets not found**
```bash
npm run dev  # or npm run build
```

**Livewire not updating**
```bash
php artisan optimize:clear
```

**Queue jobs not processing**
```bash
php artisan queue:work
```

**PDF text extraction fails**
- Ensure PDF is text-based (not scanned image)
- Check file permissions on storage directory

**AI analysis fails**
- Verify API key is valid
- Check API rate limits
- Review logs: `storage/logs/laravel.log`

## 📚 Documentation

- **[CLAUDE.md](CLAUDE.md)** - Development guidelines and conventions
- **[SETUP_CV_ANALYSIS.md](SETUP_CV_ANALYSIS.md)** - AI analysis setup guide
- **[CV_ANALYSIS_UPDATES.md](CV_ANALYSIS_UPDATES.md)** - Recent feature updates

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Run tests and format code:
   ```bash
   php artisan test
   vendor/bin/pint --dirty
   ```
4. Commit your changes (`git commit -m 'Add amazing feature'`)
5. Push to the branch (`git push origin feature/amazing-feature`)
6. Open a Pull Request

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Livewire](https://livewire.laravel.com) - Full-stack framework for Laravel
- [Flux UI](https://flux.laravel.com) - Beautiful UI components
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Laravel Prism](https://github.com/echolabsdev/prism) - AI integration
- [Anthropic Claude](https://www.anthropic.com) - AI analysis

## 📞 Support

For issues and questions:
- Check the [documentation files](#-documentation)
- Review [troubleshooting section](#-troubleshooting)
- Open an issue on GitHub

---

<p align="center">Made with ❤️ using Laravel</p>
