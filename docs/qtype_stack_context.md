Context for the STACK (maths) question type

Page type;page-question-type-stack

# Creating STACK Questions for Mathematics in Moodle

## Introduction to STACK

STACK (System for Teaching and Assessment using a Computer algebra Kernel) is a Moodle question type for that enables the creation of mathematical questions with computer algebra system (CAS) support. 

The main documentation can be found at https://docs.stack-assessment.org/en/
## Core Components of STACK Questions

### Question Variables
STACK questions use a dedicated section for defining variables using Maxima syntax. These variables can be mathematical expressions, numbers, lists, or matrices. For example:
```
p: 2*x+3;
q: expand((x+1)*(x+2));
```
Variables can be randomized using functions like `rand()` or `random_permutation()` wherever possible use `rand_with_step(lower, upper, step) when creating different question variants.

Comments are in the form /* comment here */

### Question Text
The question text area supports HTML and LaTeX for mathematical notation. Use `\( ... \)` for inline mathematics and  for display mathematics. This area presents the problem to students and can include  `{#variable#]`  for simple value or variable  substitution without LaTex rendering syntax and to  insert computed values `{@variable@}` 

### Input Areas
STACK provides various input types:
- **Algebraic Input**: For mathematical expressions
- **Textarea**: For longer responses
- **Single Character**: For single letters or numbers
- **Notes**: For comments (not graded)
- **Matrix**: For matrix input
- **String**: For text input
- **Numerical**: For numbers

Each input requires a unique name and can have specific validation settings.

### Potential Response Trees (PRTs)
PRTs are the heart of STACK's assessment logic. They evaluate student responses using a tree structure of nodes, where each node:
1. Takes inputs and applies answer tests (like `EqualComAss` for algebraic equivalence)
2. Provides feedback based on the test results
3. Directs flow to subsequent nodes or ends the tree

Common answer tests include:
- `AlgEquiv`: Algebraic equivalence
- `EqualComAss`: Equality up to commutativity and associativity
- `SubstEquiv`: Substitution equivalence
- `SysEquiv`: System equivalence for equations

## JSXGraph Integration for Interactive Visualizations

STACK excels at incorporating dynamic geometry and interactive graphs through JSXGraph, a powerful JavaScript library for interactive mathematics.

### Basic JSXGraph Setup
To include JSXGraph in your question, place the following structure in your question text:

```html
[[jsxgraph width="500px" height="400px" input-ref="inputname"]]
// JavaScript code here
[[/jsxgraph]]
```
Ensure a line break after the terminating semi colon of javascript lines.

The `input-ref` attribute links the graph to a specific input field, enabling bidirectional communication.

### Question variables in JSXGraph
To use a question variable such as one  called n into a JSXGraph block it is included as {#n#}

### Creating Interactive Elements
Within the JSXGraph block, you can create geometric constructions:

```javascript
// Create board
var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-5, 5, 5, -5], axis: true});

// Add draggable point
var point = board.create('point', [1, 2], {name: 'A'});

// Add line through origin and point
var line = board.create('line', [[0,0], point], {straightFirst: false, straightLast: false});
```

### Linking Graphs to Inputs
To make graphs interactive with student input, use event handlers:

```javascript
point.on('drag', function() {
    // Update input field when point is dragged
    stack_jxg.bind_point_input('inputname', point);
});
```

This automatically updates the specified input field with the point's coordinates.

### Advanced JSXGraph Features
You can create complex mathematical visualizations:
- Function plotting with `board.create('functiongraph', [function(x) { return x*x; }])`
- Sliders for parameter control: `board.create('slider', [[1,2],[3,2],[0,1,5]])`
- Geometric constructions: circles, polygons, conic sections
- Dynamic text that updates with geometric properties

### Communication Between JSXGraph and STACK
Use `stack_jxg` helper functions to facilitate communication:
- `stack_jxg.bind_point_input()` for point coordinates
- `stack_jxg.bind_slider_input()` for slider values
- Custom event handlers for complex interactions

## Best Practices for Mathematics Questions

### Randomization Strategies
Effective randomization prevents cheating and provides practice variety. Use:
- `rand(n)` for random integers
- `random_permutation()` for random ordering
- Constraints to ensure mathematical validity (avoiding division by zero, ensuring real roots)

### Validation Settings
Configure input validation to guide students:
- Set syntax hints for expected formats
- Enable "insert stars" to automatically add multiplication symbols
- Use "forbid floats" to require exact answers
- Configure "lowest terms" for fractions

### Feedback Design
Create meaningful feedback using:
- Specific node feedback in PRTs addressing common errors
- General feedback for overall question guidance
- Worked solutions that appear after submission

### Mathematical Notation
Use LaTeX extensively for professional presentation:
- `\frac{a}{b}` for fractions
- `\sqrt{x}` for square roots
- `\int_a^b` for integrals
- `\begin{array}{cc} ... \end{array}` for matrices

