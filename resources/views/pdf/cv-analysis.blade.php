<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Analysis - {{ $document->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #27272a;
            background: #ffffff;
            padding: 30px;
        }

        .header {
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 20pt;
            font-weight: 700;
            color: #18181b;
            margin-bottom: 4px;
        }

        .header .subtitle {
            font-size: 10pt;
            color: #71717a;
            line-height: 1.4;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: #fff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            padding: 20px;
            page-break-inside: avoid;
        }

        .score-card {
            text-align: center;
            padding: 30px 20px;
        }

        .score-gauge {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
        }

        .score-gauge svg {
            transform: rotate(-90deg);
        }

        .score-gauge-bg {
            fill: none;
            stroke: #e4e4e7;
            stroke-width: 20;
        }

        .score-gauge-progress {
            fill: none;
            stroke-width: 20;
            stroke-linecap: round;
            transition: stroke-dashoffset 0.5s;
        }

        .score-gauge-progress.excellent {
            stroke: #22c55e;
        }

        .score-gauge-progress.good {
            stroke: #eab308;
        }

        .score-gauge-progress.needs-improvement {
            stroke: #ef4444;
        }

        .score-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .score-number {
            font-size: 36pt;
            font-weight: 700;
            color: #18181b;
            line-height: 1;
        }

        .score-label {
            font-size: 9pt;
            color: #71717a;
            margin-top: 4px;
        }

        .score-description {
            font-size: 9pt;
            color: #71717a;
            line-height: 1.6;
        }

        .summary-card {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .card-section {
            flex: 1;
        }

        .card-section h3 {
            font-size: 12pt;
            font-weight: 600;
            color: #18181b;
            margin-bottom: 8px;
        }

        .card-section p {
            font-size: 9pt;
            color: #52525b;
            line-height: 1.6;
        }

        .section-divider {
            height: 1px;
            background: #e4e4e7;
        }

        .recommendations-list {
            list-style: none;
            margin-top: 10px;
        }

        .recommendations-list li {
            display: flex;
            gap: 8px;
            padding: 4px 0;
            font-size: 9pt;
            color: #52525b;
            line-height: 1.6;
        }

        .recommendations-list li .number {
            font-weight: 600;
            flex-shrink: 0;
        }

        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 12pt;
            font-weight: 600;
            color: #18181b;
            margin-bottom: 16px;
        }

        .dimension-item {
            margin-bottom: 14px;
        }

        .dimension-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .dimension-label {
            font-size: 9pt;
            font-weight: 500;
            color: #18181b;
        }

        .dimension-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: 600;
        }

        .dimension-badge.excellent {
            background: #dcfce7;
            color: #166534;
        }

        .dimension-badge.good {
            background: #dbeafe;
            color: #1e40af;
        }

        .dimension-badge.needs-improvement {
            background: #fee2e2;
            color: #991b1b;
        }

        .progress-bar-container {
            width: 100%;
            height: 8px;
            background: #e4e4e7;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 4px;
        }

        .progress-bar {
            height: 100%;
            border-radius: 4px;
        }

        .progress-bar.excellent {
            background: #22c55e;
        }

        .progress-bar.good {
            background: #3b82f6;
        }

        .progress-bar.needs-improvement {
            background: #ef4444;
        }

        .dimension-description {
            font-size: 8pt;
            color: #71717a;
            line-height: 1.4;
        }

        .penalties-list {
            list-style: none;
        }

        .penalties-list li {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            padding: 6px 0;
            font-size: 9pt;
            color: #52525b;
            line-height: 1.6;
        }

        .penalties-list li .icon {
            color: #f59e0b;
            flex-shrink: 0;
            font-size: 12pt;
            line-height: 1;
        }

        .section-analysis-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #e4e4e7;
        }

        .section-analysis-item:last-child {
            border-bottom: none;
        }

        .status-icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11pt;
            font-weight: 700;
        }

        .status-icon.success {
            color: #22c55e;
        }

        .status-icon.warning {
            color: #f59e0b;
        }

        .status-icon.error {
            color: #ef4444;
        }

        .section-analysis-content {
            flex: 1;
        }

        .section-analysis-content h4 {
            font-size: 10pt;
            font-weight: 600;
            color: #18181b;
            margin-bottom: 4px;
            text-transform: capitalize;
        }

        .section-analysis-content p {
            font-size: 9pt;
            color: #52525b;
            line-height: 1.6;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e4e4e7;
            text-align: center;
            font-size: 8pt;
            color: #a1a1aa;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>{{ $document->title }}</h1>
        <div class="subtitle">
            AI-Powered CV Analysis
            @if ($document->lastestAnalysis->analyzed_at)
                - {{ $document->lastestAnalysis->analyzed_at->format('F j, Y \a\t g:i A') }}
            @endif
        </div>
    </div>

    {{-- Score and Summary Cards --}}
    <div class="cards-grid">
        {{-- Score Gauge Card --}}
        <div class="card score-card">
            @php
                $scoreClass = $analysis['overall_score'] >= 80 ? 'excellent' : ($analysis['overall_score'] >= 60 ? 'good' : 'needs-improvement');
                $circumference = 2 * 3.14159 * 85;
                $offset = $circumference * (1 - $analysis['overall_score'] / 100);
            @endphp
            <div class="score-gauge">
                <svg width="150" height="150" viewBox="0 0 200 200">
                    <circle class="score-gauge-bg" cx="100" cy="100" r="85" />
                    <circle
                        class="score-gauge-progress {{ $scoreClass }}"
                        cx="100"
                        cy="100"
                        r="85"
                        style="stroke-dasharray: {{ $circumference }}; stroke-dashoffset: {{ $offset }};"
                    />
                </svg>
                <div class="score-text">
                    <div class="score-number">{{ $analysis['overall_score'] }}</div>
                    <div class="score-label">{{ $analysis['score_label'] }}</div>
                </div>
            </div>
            <p class="score-description">{{ $analysis['score_description'] }}</p>
        </div>

        {{-- Summary and Recommendations Card --}}
        <div class="card summary-card">
            <div class="card-section">
                <h3>Summary</h3>
                <p>{{ $analysis['summary'] }}</p>
            </div>

            <div class="section-divider"></div>

            <div class="card-section">
                <h3>Top Recommendations</h3>
                <ul class="recommendations-list">
                    @foreach ($analysis['top_recommendations'] as $index => $recommendation)
                        <li>
                            <span class="number">{{ $index + 1 }}.</span>
                            <span>{{ $recommendation }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- Scoring Dimensions --}}
    <div class="card section">
        <h2 class="section-title">Scoring Dimensions</h2>
        @foreach ($analysis['scoring_dimensions'] as $dimension)
            @php
                $dimClass = $dimension['score'] >= 80 ? 'excellent' : ($dimension['score'] >= 60 ? 'good' : 'needs-improvement');
            @endphp
            <div class="dimension-item">
                <div class="dimension-header">
                    <span class="dimension-label">{{ $dimension['label'] }}</span>
                    <span class="dimension-badge {{ $dimClass }}">{{ $dimension['score'] }}</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar {{ $dimClass }}" style="width: {{ $dimension['score'] }}%"></div>
                </div>
                @if (isset($dimension['description']))
                    <p class="dimension-description">{{ $dimension['description'] }}</p>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Penalties --}}
    @if (isset($analysis['penalties']) && count($analysis['penalties']) > 0)
        <div class="card section">
            <h2 class="section-title">Penalties</h2>
            <ul class="penalties-list">
                @foreach ($analysis['penalties'] as $penalty)
                    <li>
                        <span class="icon">⚠</span>
                        <span>{{ $penalty }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Section Analysis --}}
    <div class="card section">
        <h2 class="section-title">Section Analysis</h2>
        @foreach ($analysis['section_analysis'] as $sectionKey => $section)
            <div class="section-analysis-item">
                @php
                    $iconClass = $section['status'] === 'success' ? 'success' : ($section['status'] === 'warning' ? 'warning' : 'error');
                    $icon = $section['status'] === 'success' ? '✓' : ($section['status'] === 'warning' ? '⚠' : '✗');
                @endphp
                <div class="status-icon {{ $iconClass }}">{{ $icon }}</div>
                <div class="section-analysis-content">
                    <h4>{{ str_replace('_', ' ', $sectionKey) }}</h4>
                    <p>{{ $section['feedback'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Footer --}}
    <div class="footer">
        Generated by {{ config('app.name') }} - AI-Powered ATS Scoring
    </div>
</body>
</html>
