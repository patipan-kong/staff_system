# Project Edit - Estimated Cost Calculation Feature

## Overview
The project edit page now displays real-time estimated cost calculations based on assigned team members' hours and salaries. Costs are calculated using the formula: `(estimate hours) * (salary / (22 * 8))` where 22 represents working days per month and 8 represents hours per day.

## Features

### Individual Cost Display
- **Real-time Calculation**: Shows estimated cost immediately when hours are entered
- **Per-User Basis**: Each assigned team member has their own cost calculation
- **Formula**: Hours × (Annual Salary ÷ (22 working days × 8 hours/day))
- **Display Location**: Appears below the estimated hours input field
- **Styling**: Green text with dollar sign icon for clear identification

### Total Project Cost
- **Summary View**: Total estimated cost displayed in the project summary section
- **Automatic Updates**: Recalculates when team members are added/removed or hours change
- **Cumulative**: Sums all individual team member costs
- **Prominent Display**: Shows in the project summary card for easy visibility

## Cost Calculation Formula

### Hourly Rate Calculation
```
Hourly Rate = Annual Salary ÷ (22 working days × 8 hours per day)
Hourly Rate = Annual Salary ÷ 176 hours per month
```

### Individual Cost
```
Individual Cost = Estimated Hours × Hourly Rate
```

### Total Project Cost
```
Total Cost = Sum of all Individual Costs for assigned team members
```

## User Interface

### Individual Cost Display
Each team member's card shows:
- Estimated Hours input field
- Real-time cost calculation below (when hours > 0)
- Format: "💰 Estimated Cost: $X,XXX.XX"
- Hidden when hours are 0 or user is unassigned

### Project Summary Section
- **Total Estimated Cost**: Prominently displayed alongside team members and timeline
- **Real-time Updates**: Changes immediately when any hours are modified
- **Professional Formatting**: Currency format with proper thousands separators

## Implementation Details

### Frontend JavaScript
- **Real-time Calculation**: Event listeners on estimated hours inputs
- **Cost Functions**: 
  - `calculateHourlyCost(salary)`: Converts annual salary to hourly rate
  - `updateIndividualCost(userId)`: Updates individual team member cost
  - `updateTotalCost()`: Recalculates and updates total project cost
- **Event Handling**: Triggers on input changes and team member selection

### Data Attributes
- **Salary Data**: User salary stored in `data-salary` attribute on hours input
- **User ID**: Stored in `data-user-id` for cost calculation targeting
- **Real-time Access**: JavaScript can access salary data without additional AJAX calls

### Visual Elements
- **Cost Display**: Green text with dollar icon
- **Number Formatting**: Proper currency formatting with 2 decimal places
- **Conditional Display**: Only shows when meaningful (hours > 0, salary > 0)

## User Experience

### When Assigning Team Members
1. User checks team member checkbox
2. Estimated hours input field appears
3. User enters estimated hours
4. Individual cost appears immediately below hours input
5. Total project cost updates in summary section

### When Changing Hours
1. User modifies estimated hours for any team member
2. Individual cost updates instantly
3. Total project cost recalculates automatically
4. All changes reflected in real-time

### Cost Visibility
- **Individual Level**: Each team member shows their estimated cost
- **Project Level**: Total cost visible in summary section
- **Immediate Feedback**: No delays or page refreshes required

## Benefits

### Project Planning
- **Budget Estimation**: Immediate visibility of project cost implications
- **Resource Planning**: Understand cost impact of team composition
- **Decision Support**: Compare cost of different team configurations

### Management Insight
- **Cost Awareness**: Real-time understanding of project expenses
- **Team Optimization**: Balance skills vs. cost when assigning members
- **Budget Control**: Ensure projects stay within financial constraints

### User Experience
- **Instant Feedback**: No waiting for calculations
- **Clear Display**: Easy to understand cost breakdowns
- **Professional Interface**: Clean, intuitive design

## Technical Notes

### Salary Requirements
- Feature requires user salary data to be available
- Calculates based on annual salary figures
- Handles cases where salary data is missing (shows $0.00)

### Calculation Assumptions
- **Working Days**: 22 days per month (industry standard)
- **Daily Hours**: 8 hours per day (standard full-time)
- **Monthly Hours**: 176 total working hours per month

### Browser Support
- Modern JavaScript (ES6+) for event handling
- Real-time DOM manipulation
- Responsive design for all screen sizes

## Usage Example

### Scenario
- Team Member A: $50,000 salary, 40 hours estimated
- Team Member B: $75,000 salary, 30 hours estimated

### Calculations
- Team Member A: 40 × ($50,000 ÷ 176) = 40 × $284.09 = $11,363.60
- Team Member B: 30 × ($75,000 ÷ 176) = 30 × $426.14 = $12,784.20
- **Total Project Cost**: $24,147.80

This provides immediate cost visibility for informed project planning and budget management.
