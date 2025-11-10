<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Role Analysis</title>
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
            text-align: center;
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

        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
            background: #fff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            padding: 20px;
        }

        .section-title {
            font-size: 12pt;
            font-weight: 600;
            color: #18181b;
            margin-bottom: 12px;
        }

        .section-subtitle {
            font-size: 9pt;
            color: #71717a;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .section-content {
            font-size: 9pt;
            color: #52525b;
            line-height: 1.6;
        }

        .takeaway-box {
            background: #dbeafe;
            border: 1px solid #93c5fd;
            border-radius: 6px;
            padding: 12px;
            margin-top: 16px;
        }

        .takeaway-box .title {
            font-size: 9pt;
            font-weight: 600;
            color: #1e3a8a;
            margin-bottom: 4px;
        }

        .takeaway-box .content {
            font-size: 9pt;
            color: #1e40af;
            line-height: 1.6;
        }

        .keywords-list {
            list-style: none;
        }

        .keywords-list li {
            display: flex;
            gap: 8px;
            padding: 8px 0;
            border-bottom: 1px solid #f4f4f5;
        }

        .keywords-list li:last-child {
            border-bottom: none;
        }

        .keywords-list .number {
            font-weight: 600;
            color: #3b82f6;
            flex-shrink: 0;
        }

        .keywords-list .content {
            flex: 1;
        }

        .keywords-list .keyword {
            font-weight: 600;
            color: #18181b;
            display: block;
            margin-bottom: 2px;
        }

        .keywords-list .explanation {
            font-size: 8pt;
            color: #71717a;
            line-height: 1.4;
        }

        .skills-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .skill-item {
            padding: 10px 0;
            border-bottom: 1px solid #f4f4f5;
        }

        .skill-item:last-child {
            border-bottom: none;
        }

        .skill-header {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            margin-bottom: 6px;
        }

        .skill-header .number {
            font-weight: 600;
            color: #3b82f6;
            flex-shrink: 0;
        }

        .skill-header .title {
            font-size: 10pt;
            font-weight: 600;
            color: #18181b;
        }

        .skill-description {
            font-size: 8pt;
            color: #71717a;
            line-height: 1.4;
            margin-bottom: 8px;
            padding-left: 16px;
        }

        .skill-example {
            background: #f4f4f5;
            border: 1px solid #e4e4e7;
            border-radius: 4px;
            padding: 8px;
            margin-left: 16px;
        }

        .skill-example .label {
            font-size: 8pt;
            font-weight: 600;
            color: #71717a;
            margin-bottom: 4px;
        }

        .skill-example .text {
            font-size: 8pt;
            color: #52525b;
            font-style: italic;
            line-height: 1.4;
        }

        .recommendation-section {
            margin-top: 16px;
        }

        .recommendation-section .label {
            font-size: 9pt;
            font-weight: 600;
            color: #18181b;
            margin-bottom: 8px;
            display: block;
        }

        .recommendation-section .content {
            font-size: 9pt;
            color: #52525b;
            line-height: 1.6;
            padding-left: 16px;
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
        <h1>AI Role Analysis</h1>
        <div class="subtitle">
            Generated on {{ date('F j, Y \a\t g:i A') }}
        </div>
    </div>

    {{-- Comprehensive Overview --}}
    @if (isset($analysis['comprehensive_overview']))
        <div class="section">
            <h2 class="section-title">Comprehensive Overview</h2>
            <div class="section-content">
                {{ $analysis['comprehensive_overview']['summary'] ?? 'No summary available.' }}
            </div>

            @if (isset($analysis['comprehensive_overview']['actionable_takeaway']))
                <div class="takeaway-box">
                    <div class="title">Actionable Takeaway</div>
                    <div class="content">{{ $analysis['comprehensive_overview']['actionable_takeaway'] }}</div>
                </div>
            @endif
        </div>
    @endif

    {{-- Keywords --}}
    @if (isset($analysis['keywords']) && count($analysis['keywords']) > 0)
        <div class="section">
            <h2 class="section-title">Keywords</h2>
            <div class="section-subtitle">
                List the 10 most relevant keywords and phrases (including variations) that a candidate should emphasize in their CV and cover letter.
            </div>

            <ul class="keywords-list">
                @foreach ($analysis['keywords'] as $index => $keyword)
                    <li>
                        <span class="number">{{ $index + 1 }}.</span>
                        <div class="content">
                            <span class="keyword">{{ $keyword['keyword'] }}</span>
                            <span class="explanation">{{ $keyword['explanation'] }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="takeaway-box">
                <div class="title">Actionable Takeaway</div>
                <div class="content">Prioritize these keywords throughout your CV and cover letter, tailoring your language to match the job description's terminology.</div>
            </div>
        </div>
    @endif

    {{-- Hard Skills and Soft Skills --}}
    @if ((isset($analysis['hard_skills']) && count($analysis['hard_skills']) > 0) || (isset($analysis['soft_skills']) && count($analysis['soft_skills']) > 0))
        <div class="skills-grid">
            {{-- Hard Skills --}}
            @if (isset($analysis['hard_skills']) && count($analysis['hard_skills']) > 0)
                <div class="section">
                    <h2 class="section-title">Hard Skills</h2>
                    <div class="section-subtitle">
                        Critical technical or job-specific skills required for success in this role.
                    </div>

                    @foreach ($analysis['hard_skills'] as $index => $skill)
                        <div class="skill-item">
                            <div class="skill-header">
                                <span class="number">{{ $index + 1 }}.</span>
                                <span class="title">{{ $skill['skill'] }}</span>
                            </div>
                            <div class="skill-description">
                                {{ $skill['description'] }}
                            </div>
                            @if (isset($skill['example']))
                                <div class="skill-example">
                                    <div class="label">Example:</div>
                                    <div class="text">"{{ $skill['example'] }}"</div>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <div class="takeaway-box">
                        <div class="title">Actionable Takeaway</div>
                        <div class="content">Showcase your technical skills with specific examples of how you've applied them to solve problems and achieve results.</div>
                    </div>
                </div>
            @endif

            {{-- Soft Skills --}}
            @if (isset($analysis['soft_skills']) && count($analysis['soft_skills']) > 0)
                <div class="section">
                    <h2 class="section-title">Soft Skills</h2>
                    <div class="section-subtitle">
                        Important interpersonal and communication skills needed for this position.
                    </div>

                    @foreach ($analysis['soft_skills'] as $index => $skill)
                        <div class="skill-item">
                            <div class="skill-header">
                                <span class="number">{{ $index + 1 }}.</span>
                                <span class="title">{{ $skill['skill'] }}</span>
                            </div>
                            <div class="skill-description">
                                {{ $skill['description'] }}
                            </div>
                            @if (isset($skill['example']))
                                <div class="skill-example">
                                    <div class="label">Example:</div>
                                    <div class="text">"{{ $skill['example'] }}"</div>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <div class="takeaway-box">
                        <div class="title">Actionable Takeaway</div>
                        <div class="content">Weave stories into your CV and cover letter that demonstrate your ability to work effectively with others, solve problems, and communicate clearly.</div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Ideal Candidate Profile --}}
    @if (isset($analysis['ideal_candidate_profile']))
        <div class="section">
            <h2 class="section-title">Ideal Candidate Profile and Tailoring Recommendations</h2>
            <div class="section-subtitle">
                Summarize the key attributes, experiences, and motivations of an ideal candidate.
            </div>

            @if (isset($analysis['ideal_candidate_profile']['summary']))
                <div class="section-content">
                    {{ $analysis['ideal_candidate_profile']['summary'] }}
                </div>
            @endif

            @if (isset($analysis['ideal_candidate_profile']['tailoring_recommendations']))
                <div class="recommendation-section">
                    <span class="label">Tailoring Recommendations:</span>

                    @if (isset($analysis['ideal_candidate_profile']['tailoring_recommendations']['cv']))
                        <div style="margin-bottom: 12px;">
                            <strong style="font-size: 9pt; color: #18181b;">CV:</strong>
                            <div class="content" style="margin-top: 4px;">
                                {{ $analysis['ideal_candidate_profile']['tailoring_recommendations']['cv'] }}
                            </div>
                        </div>
                    @endif

                    @if (isset($analysis['ideal_candidate_profile']['tailoring_recommendations']['cover_letter']))
                        <div>
                            <strong style="font-size: 9pt; color: #18181b;">Cover Letter:</strong>
                            <div class="content" style="margin-top: 4px;">
                                {{ $analysis['ideal_candidate_profile']['tailoring_recommendations']['cover_letter'] }}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="takeaway-box">
                    <div class="title">Actionable Takeaway</div>
                    <div class="content">Position yourself as a well-rounded candidate who is not only technically skilled but also aligned with the role's requirements and eager to contribute to the organization's mission.</div>
                </div>
            @endif
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Generated by {{ config('app.name') }} - This analysis was generated using AI and should be used as a guide.
    </div>
</body>
</html>
