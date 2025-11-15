# 📋 Job Application Tracker

> A powerful Laravel 12 application for tracking job applications with AI-powered CV analysis, role analysis, profile matching, and cover letter generation. Built with modern tools and best practices.

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php)](https://php.net)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=flat)](https://livewire.laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## ✨ Features

### 📊 Job Application Management
- Track applications through complete hiring lifecycle
- Status workflow: Applied → Screening → Interview → Technical Test → Final Interview → Offer → Accepted/Rejected/Withdrawn
- Priority levels (High, Medium, Low) with visual indicators
- Job types: Full Time, Part Time, Freelance, Fixed Term, Contract
- Location and work arrangement tracking
- Salary range tracking (min/max)
- Advanced filtering and search
- Tagging system for organization
- Multiple date tracking (application, interviews, follow-ups, deadlines)
- Document attachment to applications

### 📄 Document Management
- PDF document upload and storage (user-specific folders)
- Multiple document types: CV, Cover Letter, Portfolio, References, Certificates, Other
- File size formatting and metadata tracking
- Secure file storage with hash-based integrity
- Download and delete operations
- Link documents to multiple job applications
- Document filtering and search

### 🤖 AI-Powered Features

#### CV Analysis
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

#### Role Analysis
- Analyze job descriptions and requirements
- Extract key responsibilities and qualifications
- Identify required skills and experience levels
- Understand role seniority and expectations
- Rate limiting (10 analyses per user per day)
- Results cached in job application records

#### Profile Matching
- Compare your profile against job requirements
- Skill gap analysis
- Qualification alignment scoring
- Experience level matching
- Personalized recommendations for improvement

#### Cover Letter Generation
- AI-generated personalized cover letters
- Tailored to specific job descriptions
- Highlights relevant skills and experience
- Professional formatting and tone
- Customizable and editable output

### 🔐 Authentication & Security
- Laravel Fortify authentication with 2FA support
- Email verification
- Password reset functionality
- Session management
- User-specific data isolation

### 🎨 Modern UI/UX
- Flux Pro UI components (premium component library)
- Tailwind CSS 4.x styling with custom design system
- Dark mode support throughout
- Responsive design for all screen sizes
- Real-time updates with Livewire 3
- Toast notifications for user feedback
- Confirmation modals for destructive actions
- Interactive dashboard with widgets and charts
- Tabbed interface for complex views
- Breadcrumb navigation
- Loading states and skeleton screens

## 🚀 Quick Start

### Prerequisites

- **PHP** >= 8.4 with required extensions (BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML)
- **Composer** >= 2.6
- **Node.js** >= 20 and npm >= 10
- **Database**: SQLite (quickstart) or MySQL/PostgreSQL
- **AI Provider** (optional): Anthropic Claude, OpenAI, or Google Gemini API key for AI features

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

#### AI Analysis (Required for AI Features)
```env
# Choose your preferred AI provider
# The same provider will be used for:
# - CV Analysis
# - Role Analysis
# - Profile Matching
# - Cover Letter Generation

# Option 1: Anthropic Claude (Recommended)
PRISM_PROVIDER=anthropic
ANTHROPIC_API_KEY=your_anthropic_api_key

# Option 2: OpenAI
# PRISM_PROVIDER=openai
# OPENAI_API_KEY=your_openai_api_key

# Option 3: Google Gemini
# PRISM_PROVIDER=gemini
# GEMINI_API_KEY=your_gemini_api_key
```

**Get API Keys:**
- Anthropic Claude: https://console.anthropic.com/ (Recommended for best results)
- OpenAI: https://platform.openai.com/api-keys
- Google Gemini: https://ai.google.dev/

**Rate Limiting:**
- CV Analysis: Unlimited (cached in database)
- Role Analysis: 10 per user per day
- Profile Matching: Based on role analysis
- Cover Letter: Based on role analysis and profile matching

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
| **Backend** | Laravel 12, PHP 8.4 |
| **Frontend** | Livewire 3, Volt, Flux Pro UI |
| **Styling** | Tailwind CSS 4.x |
| **Build** | Vite 7 |
| **Database** | SQLite / MySQL / PostgreSQL |
| **Testing** | Pest 4 with Browser Testing |
| **AI** | Laravel Prism (Anthropic/OpenAI/Gemini) |
| **PDF** | Spatie Laravel PDF, Browsershot |
| **Authentication** | Laravel Fortify with 2FA |
| **Development** | Laravel Nightwatch, Laravel Pail, Clockwork |

### Project Structure

```
job_tracker/
├── app/
│   ├── Enums/
│   │   ├── ApplicationPriority.php  # High, Medium, Low
│   │   ├── ApplicationStatus.php    # Complete workflow
│   │   ├── DocumentType.php         # CV, Cover Letter, etc.
│   │   └── JobType.php              # Full Time, Part Time, etc.
│   ├── Livewire/
│   │   ├── AI/
│   │   │   └── RoleAnalysis.php     # AI role analysis component
│   │   ├── Application/
│   │   │   ├── CreateJobApplication.php
│   │   │   ├── EditJobApplication.php
│   │   │   ├── ListJobApplications.php
│   │   │   └── ViewApplication.php   # Full application view
│   │   ├── Document/
│   │   │   ├── AnalyzeDocument.php   # CV analysis
│   │   │   └── ListDocuments.php
│   │   ├── Forms/
│   │   │   └── JobApplicationForm.php
│   │   └── Dashboard.php
│   ├── Models/
│   │   ├── Document.php
│   │   ├── JobApplication.php
│   │   ├── JobApplicationDocument.php  # Pivot model
│   │   └── User.php
│   └── Services/
│       ├── CVAnalysis/
│       │   ├── CVAnalysisService.php
│       │   ├── Providers/
│       │   │   ├── ClaudeProvider.php
│       │   │   └── GeminiProvider.php
│       │   └── RateLimiting/
│       └── RoleAnalysis/
│           ├── RoleAnalysisService.php
│           └── DTOs/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── livewire/
│       │   ├── ai/
│       │   │   └── role-analysis.blade.php
│       │   ├── application/
│       │   │   ├── create-job-application.blade.php
│       │   │   ├── edit-job-application.blade.php
│       │   │   ├── list-job-applications.blade.php
│       │   │   ├── view-application.blade.php
│       │   │   ├── filters.blade.php
│       │   │   ├── table.blade.php
│       │   │   └── tabs/
│       │   │       ├── cover-letter.blade.php
│       │   │       ├── profile-matching.blade.php
│       │   │       └── role-analysis.blade.php
│       │   ├── auth/                # Complete auth flow
│       │   ├── dashboard.blade.php
│       │   ├── document/
│       │   └── settings/            # User settings
│       └── components/
├── routes/
│   ├── auth.php
│   └── web.php
├── tests/
│   ├── Browser/                     # Pest 4 browser tests
│   ├── Feature/
│   └── Unit/
└── CLAUDE.md                        # Development guidelines
```

### Key Components

#### Livewire Components
- **Dashboard**: Interactive dashboard with application statistics and charts
- **ListJobApplications**: Browse, filter, search, and manage applications
- **CreateJobApplication**: Form for adding new job applications
- **EditJobApplication**: Form for updating existing applications
- **ViewApplication**: Detailed view with tabs for overview, role analysis, profile matching, and cover letter
- **ListDocuments**: Document management with upload, download, delete
- **AnalyzeDocument**: AI-powered CV analysis with caching
- **RoleAnalysis**: AI analysis of job descriptions and requirements

#### Services
- **CVAnalysisService**: Handles CV analysis with multiple AI providers
- **RoleAnalysisService**: Analyzes job descriptions with rate limiting
- **AIProviderInterface**: Abstraction for different AI providers (Claude, Gemini)
- **AnalysisRateLimiter**: Rate limiting for AI operations

#### Enums
- **ApplicationStatus**: Complete workflow (9 statuses)
- **ApplicationPriority**: High, Medium, Low with colors
- **DocumentType**: CV, Cover Letter, Portfolio, References, Certificates, Other
- **JobType**: Full Time, Part Time, Freelance, Fixed Term, Contract

#### Models
- **User**: Authentication with 2FA, relationships to applications and documents
- **JobApplication**: Job applications with AI analysis results (role_analysis, profile_matching, cover_letter)
- **Document**: PDF documents with CV analysis caching
- **JobApplicationDocument**: Pivot model for many-to-many relationship

## 🤖 AI Features in Detail

### CV Analysis
Analyzes uploaded CV/Resume documents to provide comprehensive feedback.

**Features:**
- Extracts text from PDF files
- Analyzes with Claude/GPT/Gemini
- Generates comprehensive ATS scores (0-100)
- 8 scoring dimensions with detailed feedback
- Top 3 actionable recommendations
- Section-by-section analysis
- Caches results in database

**How to Use:**
1. Navigate to Documents page
2. Upload your CV (PDF format)
3. Click "Analyze Document"
4. View comprehensive analysis results
5. Re-analyze anytime without additional cost (cached)

### Role Analysis
Analyzes job descriptions to extract key information and requirements.

**Features:**
- Extracts role responsibilities
- Identifies required skills and qualifications
- Determines experience level and seniority
- Highlights must-have vs nice-to-have requirements
- Rate limited to 10 analyses per user per day
- Results cached in job application

**How to Use:**
1. Create or edit a job application
2. Add a detailed job description (min 100 characters)
3. Navigate to the "Role Analysis" tab
4. Click "Analyze Role"
5. View extracted insights

### Profile Matching
Compares your profile against job requirements to identify gaps and strengths.

**Features:**
- Skill gap analysis
- Experience level matching
- Qualification alignment scoring
- Personalized improvement recommendations
- Based on role analysis results

**How to Use:**
1. Complete role analysis for a job application
2. Navigate to "Profile Matching" tab
3. Click "Match Profile"
4. Review your compatibility score and recommendations

### Cover Letter Generation
Generates personalized, professional cover letters tailored to specific jobs.

**Features:**
- AI-generated content based on job requirements
- Highlights relevant skills and experience
- Professional tone and formatting
- Customizable and editable
- Based on role analysis and profile matching

**How to Use:**
1. Complete role analysis and profile matching
2. Navigate to "Cover Letter" tab
3. Click "Generate Cover Letter"
4. Review and customize the generated letter
5. Copy or download for use

### AI Cost Optimization

| Feature | Provider | Cost per Use | Speed | Caching |
|---------|----------|--------------|-------|---------|
| **CV Analysis** | Claude 3.5 Sonnet | ~$0.015 | Fast | ✅ Yes (permanent) |
| **CV Analysis** | GPT-4 | ~$0.30 | Medium | ✅ Yes (permanent) |
| **CV Analysis** | Gemini Pro | ~$0.001 | Fast | ✅ Yes (permanent) |
| **Role Analysis** | Claude 3.5 Sonnet | ~$0.02 | Fast | ✅ Yes (per job) |
| **Profile Matching** | Claude 3.5 Sonnet | ~$0.03 | Fast | ✅ Yes (per job) |
| **Cover Letter** | Claude 3.5 Sonnet | ~$0.04 | Fast | ✅ Yes (per job) |

**Note:** All AI results are cached in the database, so re-running analysis on the same content is free and instant.

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

## 📊 Dashboard

The application includes an interactive dashboard with real-time insights:

### Dashboard Widgets

**Applications by Status**
- Visual pie chart showing distribution across all statuses
- Color-coded segments for easy identification
- Legend with counts for each status
- Updates in real-time as applications change

**Applications by Priority**
- Pie chart showing High/Medium/Low priority distribution
- Helps identify focus areas
- Quick overview of workload

**Recent Applications**
- List of most recently added applications
- Quick access to latest entries
- Status badges for each application

### Quick Stats
- Total applications
- Applications by status breakdown
- Applications by priority breakdown
- Recent activity timeline

## 📚 Documentation

- **[CLAUDE.md](CLAUDE.md)** - Comprehensive development guidelines, conventions, and Laravel Boost integration

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

## 🎯 Key Highlights

### What Makes This Application Special

- **Modern Laravel 12**: Uses the latest streamlined structure with no deprecated files
- **AI-First Approach**: Four distinct AI features working together seamlessly
- **Cost-Effective**: Intelligent caching reduces AI costs by ~99% after first use
- **Type-Safe**: Strict typing throughout with PHP 8.4 features
- **Production-Ready**: Comprehensive test coverage with Pest 4
- **Developer-Friendly**: Laravel Boost integration with enhanced tooling
- **Beautiful UI**: Premium Flux Pro components with dark mode
- **Real-Time**: Livewire 3 for instant updates without page refreshes
- **Scalable Architecture**: Service layer pattern with DTOs and interfaces
- **Security-First**: 2FA support, email verification, secure file storage

### Advanced Features

- **Rate Limiting**: Intelligent rate limiting for AI features to prevent abuse
- **Caching Strategy**: Multi-level caching (database, application) for performance
- **Queue Support**: Background processing for long-running operations
- **Multi-Provider AI**: Easily switch between Anthropic, OpenAI, and Gemini
- **Document Relationships**: Many-to-many relationship between jobs and documents
- **Comprehensive Enums**: Type-safe enums with helper methods for UI rendering

## 📖 User Journey

### Typical Workflow

1. **Setup Profile**
   - Register and verify email
   - Enable 2FA for security (optional)
   - Configure appearance preferences

2. **Upload Documents**
   - Upload your CV/Resume
   - Analyze CV to get ATS score and recommendations
   - Upload cover letter templates, portfolio, etc.

3. **Track Job Applications**
   - Create new job application with details
   - Add job description
   - Run role analysis to understand requirements
   - Use profile matching to see how you fit
   - Generate tailored cover letter

4. **Manage Pipeline**
   - Update application status as you progress
   - Set priorities and deadlines
   - Add notes and follow-up dates
   - Attach relevant documents
   - Track multiple interview rounds

5. **Monitor Progress**
   - View dashboard for overview
   - Filter and search applications
   - Identify gaps with AI insights
   - Optimize applications based on analysis

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework for Web Artisans
- [Livewire](https://livewire.laravel.com) - Full-stack framework for Laravel
- [Livewire Volt](https://livewire.laravel.com/docs/volt) - Single-file Livewire components
- [Flux UI](https://flux.laravel.com) - Beautiful UI component library
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Laravel Prism](https://github.com/echolabsdev/prism) - Multi-provider AI integration
- [Laravel Fortify](https://laravel.com/docs/fortify) - Backend authentication
- [Spatie Laravel PDF](https://github.com/spatie/laravel-pdf) - PDF generation
- [Pest PHP](https://pestphp.com) - Testing framework
- [Anthropic Claude](https://www.anthropic.com) - Advanced AI models
- [Laravel Boost](https://github.com/laravel/boost) - Enhanced development tooling

## 📞 Support

For issues and questions:
- Check the [documentation files](#-documentation)
- Review [troubleshooting section](#-troubleshooting)
- Open an issue on GitHub

---

<p align="center">Made with ❤️ using Laravel</p>
