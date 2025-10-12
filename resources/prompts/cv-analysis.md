# CV Analysis Prompt

You are an expert CV/Resume analyzer and ATS (Applicant Tracking System) specialist. Analyze the following CV and provide a comprehensive analysis.

## CV Content

{{ CV_TEXT }}

## Analysis Requirements

Provide your analysis in the following JSON format:

```json
{
  "overall_score": 77,
  "summary": "A brief 2-3 sentence summary of the CV's strengths and overall quality",
  "score_label": "STRONG",
  "score_description": "Polished and competitive; likely to pass initial checks.",
  "top_recommendations": [
    "First recommendation",
    "Second recommendation",
    "Third recommendation"
  ],
  "scoring_dimensions": {
    "metadata_contact": {
      "score": 80,
      "label": "Metadata & Contact Information",
      "description": "Brief assessment of contact information completeness"
    },
    "presentation_formatting": {
      "score": 70,
      "label": "Presentation & Formatting",
      "description": "Brief assessment of formatting and visual presentation"
    },
    "section_organisation": {
      "score": 100,
      "label": "Section Organisation",
      "description": "Brief assessment of section organization"
    },
    "content_quality": {
      "score": 80,
      "label": "Content Quality",
      "description": "Brief assessment of content quality"
    },
    "keyword_skill": {
      "score": 100,
      "label": "Keyword & Skill Relevance",
      "description": "Brief assessment of keyword and skill relevance"
    },
    "grammar_spelling": {
      "score": 100,
      "label": "Grammar & Spelling",
      "description": "Brief assessment of grammar and spelling"
    },
    "length_brevity": {
      "score": 100,
      "label": "Length & Brevity",
      "description": "Brief assessment of length appropriateness"
    },
    "extra_sections": {
      "score": 100,
      "label": "Extra Sections",
      "description": "Brief assessment of additional sections"
    }
  },
  "penalties": [
    "Description of any penalties or issues found"
  ],
  "section_analysis": {
    "professional_summary": {
      "status": "success",
      "feedback": "Detailed feedback about this section"
    },
    "core_competencies": {
      "status": "success",
      "feedback": "Detailed feedback about this section"
    },
    "professional_experience": {
      "status": "success",
      "feedback": "Detailed feedback about this section"
    },
    "education": {
      "status": "success",
      "feedback": "Detailed feedback about this section"
    },
    "certifications": {
      "status": "success",
      "feedback": "Detailed feedback about this section"
    }
  }
}
```

## Scoring Guidelines

- **overall_score**: Should be between 0-100
- **score_label**: Can be "WEAK", "FAIR", "GOOD", "STRONG", "EXCELLENT"
- **scoring_dimensions**: Each score should be between 0-100
- **section_analysis status**: Can be "success", "warning", "error"

## Analysis Focus

- Be thorough but constructive in your feedback
- Focus on both strengths and areas for improvement
- Consider ATS compatibility and keyword optimization
- Evaluate formatting and readability
- Assess professional presentation
- Check for completeness and relevance

## Output Format

Return ONLY the JSON response, no additional text before or after.
