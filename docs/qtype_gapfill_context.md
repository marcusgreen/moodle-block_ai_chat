# Gapfill Question Type – Additional Context for AI Assistance

This document provides supplementary context about the Moodle **Gapfill** question type (`qtype_gapfill`). It is intended to be used alongside the strings found in the question editing form to help an AI assistant give accurate, useful guidance to teachers authoring Gapfill questions.

---

## Overview

Gapfill is a fill-in-the-blanks (cloze) question type for Moodle. Its defining feature is an extremely simple authoring syntax: **put square brackets around any word or phrase you want to become a gap**. For example:

> `The [cat] sat on the [mat].`

This creates a question with two gaps. Students either type their answers into text fields, choose from a dropdown list, or drag and drop labelled tiles, depending on the display mode chosen.

The plugin is designed to be beginner-friendly for teachers while still supporting advanced features such as regular-expression matching, alternative correct answers, distractor management, per-gap settings, and interactive hint delivery.

---

## Authoring Syntax

### Basic gaps
Wrap the correct answer in square brackets anywhere in the question text:

```
The [dog] chased the [cat] up the [tree].
```

### Custom delimiters
If square brackets conflict with the subject matter (e.g. programming or maths questions), an alternative delimiter character can be chosen in the question editing form. For example, using `#` as the delimiter:

```
The #dog# chased the #cat# up the #tree#.
```

### Multiple acceptable answers (pipe operator `|`)
Use `|` inside a gap to list equivalent correct answers. The student's response is accepted if it matches **any** of the alternatives:

```
The primary colours are [red|blue|yellow].
```

In dropdown or drag-drop mode, the pipe-separated values also become the pool of answer choices displayed to the student.

### Accepting any answer (`[.+]`)
The pattern `[.+]` makes a gap accept any non-empty response. This is useful for open-ended or opinion questions where any answer should receive credit. When shown as feedback, the `.+` is hidden from students.

### Blank / empty gap (`!!`)
Using `!!` inside a gap (e.g. `[!!]`) represents an intentionally blank field. This can be used in conjunction with the "No Duplicates" feature.

---

## Answer Display Modes

The **Answer display** setting in the editing form controls how students interact with gaps. There are three modes:

| Mode | Description |
|------|-------------|
| **Gapfill** (default) | Students type directly into text input boxes embedded in the question text. |
| **Dropdown** | A dropdown select list appears at each gap position, populated with a shuffled mix of correct answers and any wrong answers (distractors) added in the form. |
| **Drag and drop** | Labelled tiles appear (by default below the question text, or inline if *Options after text* is unchecked). Students drag tiles into the correct gap positions. Works on touchscreens and the Moodle mobile app. |

---

## Key Options in the Editing Form

### Case Sensitive
When enabled, answers must exactly match the case of the stored answer. For example, if the correct answer is `CAT`, then `cat` or `Cat` will be marked wrong.
*Default: off (case-insensitive matching).*

### No Duplicates
When enabled, each gap must be filled with a **unique** answer. If several gaps share the same pool of alternatives (e.g. `[gold|silver|bronze]` in every gap for an Olympic medals question), a student who enters `gold` in every field will only receive a mark for the **first** correct use. Subsequent identical entries still show a tick visually but do not score.
*Useful for: exercises where each item should be assigned only once.*

### Disable Regular Expressions
By default, Gapfill uses PHP regular expressions to compare the student's input with the stored answer, which allows flexible matching. Enabling **Disable regex** switches to a plain string comparison. This is recommended for:
- Maths questions containing characters that have special regex meaning (`*`, `?`, `+`, `(`, `)`, etc.)
- HTML or programming questions
- Any question where literal character matching is required

*The `|` pipe operator still works as an "or" even when regex is disabled.*

### Fixed Gap Size
When enabled, all gaps in the question are rendered at the same width — the width of the **longest** gap. This prevents students from guessing short answers by noticing that a gap is narrow. When disabled, each gap's width reflects the length of its own answer (or the longest alternative if the pipe operator is used).

### Options After Text
Controls where drag-and-drop tiles and dropdown answer pools appear:
- **Checked**: answer options are displayed **below** the question body text.
- **Unchecked**: answer options appear **inline**, within the question text at each gap position.

*Only relevant for Dropdown and Drag-and-drop display modes.*

### Letter Hints (Multiple Tries)
When the question behaviour is set to **Interactive with multiple tries**, enabling Letter Hints causes the system to automatically insert hint text into the "Multiple tries" hint boxes. On each failed attempt, the student is shown incrementing letters from the correct answer (e.g. first attempt reveals `c`, second attempt `ca`, and so on).
*Requires the question behaviour to be "Interactive with multiple tries" to have any effect. A site-wide default can be set by the administrator in plugin settings.*

### Single Use Draggables
When using drag-and-drop mode, enabling this option removes a tile from the available pool once it has been placed in a gap, preventing a student from using the same tile twice. This reinforces that each answer option should be used only once.

---

## Wrong Answers (Distractors)

For **Dropdown** and **Drag-and-drop** modes, the editing form provides a field to enter **wrong answers** (distractors). These are mixed in with the correct answers to form the pool of choices presented to the student. Adding plausible distractors increases the challenge of the question and prevents students from succeeding simply by process of elimination when there are few options.

---

## Per-Gap Settings (Add Gap Settings)

Individual gaps can be given their own settings (such as a specific mark weight or display override) using the **Add Gap settings** section of the form. This allows some gaps in a question to be weighted differently or behave differently from the defaults set for the whole question.

---

## Grading

- Each gap is marked independently as either fully correct (1) or fully incorrect (0).
- The overall question score is the proportion of gaps answered correctly, multiplied by the question's total marks.
- When **No Duplicates** is active, duplicate responses beyond the first do not contribute marks even if the answer would otherwise be correct.
- Partial credit is awarded automatically in proportion to the number of gaps correct.

---

## Regular Expressions in Gaps

Unless **Disable regex** is checked, the content inside brackets is treated as a PHP regular expression pattern for matching. This enables:
- `[colou?r]` — matches both `color` and `colour`
- `[.+]` — matches any non-empty string
- `[\d+]` — matches one or more digits

Teachers unfamiliar with regular expressions should enable **Disable regex** to ensure literal string matching.

---

## Import Examples

The plugin ships with a set of example questions in XML format (found in `examples/en/gapfill_examples.xml`). These can be imported into a Moodle course category from the **Import Examples** page, accessible from the plugin settings. This is a helpful way to see the range of features in action.

---

## Mobile App Support

Gapfill is compatible with the Moodle mobile app. Drag-and-drop functionality uses touch-friendly JavaScript and CSS, so all three display modes (gapfill, dropdown, drag-drop) work on smartphones and tablets through the app as well as in a desktop browser.

---

## Interaction with Question Behaviours

Gapfill is compatible with all standard Moodle question behaviours:
- **Deferred feedback** — all gaps submitted together at the end.
- **Immediate feedback** — feedback given after each submission.
- **Interactive with multiple tries** — student can retry incorrect gaps; Letter Hints work only in this mode.
- **Adaptive mode** — similar to interactive, allows multiple attempts with penalty.

---

## Summary of Common Authoring Scenarios

| Goal | Approach |
|------|----------|
| Simple typed fill-in-the-blank | Use default Gapfill mode with `[answer]` syntax |
| Multiple valid spellings or synonyms | Use pipe operator: `[colour\|color]` |
| Dropdown choice from a fixed list | Set display to Dropdown; use `[a\|b\|c]` in each gap |
| Drag-and-drop matching | Set display to Drag-drop; add distractors in Wrong Answers field |
| Prevent case-sensitivity issues | Leave Case Sensitive unchecked (default) |
| Maths or code questions | Enable Disable Regex to avoid special character problems |
| Uniform gap width (hide length clues) | Enable Fixed Gap Size |
| Each answer used once only | Enable No Duplicates (for pools) or Single Use Draggables (for drag-drop) |
| Progressive letter hints on retry | Set behaviour to Interactive with multiple tries; enable Letter Hints |
| Accept any answer in a gap | Use `[.+]` as the gap content |