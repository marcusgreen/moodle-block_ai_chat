# AI Chat Block: Context for the Drag and Drop into Text Question Type (ddwtos)

## Overview

The **Drag and Drop into Text** question type (`ddwtos`) presents students with a passage of text that contains numbered gaps. Students drag words or phrases from a list of choices and drop them into the correct gaps. It is a core Moodle question type, originally developed by the UK Open University, and has been included in standard Moodle since version 3.0.

It is well suited to: vocabulary and grammar exercises, reading comprehension, sentence completion, technical terminology, and any task where selecting the right word or phrase from a given set is the learning goal.

Documentation: https://docs.moodle.org/501/en/Drag_and_drop_into_text_question_type
Source code: https://github.com/moodle/moodle/tree/main/public/question/type/ddwtos

---

## How the Question Works for Students

- The question text is displayed with visible blank gaps in it.
- A set of draggable word or phrase tiles is shown (typically above or beside the text).
- Students drag tiles into the gaps. On touch screens and keyboards, a tap/click-to-select then tap/click-to-place interaction is used instead of dragging.
- Students can move a tile back out of a gap by dragging a different tile into it, or by dragging the placed tile back to the choice area.
- **Keyboard accessibility**: students can press `Tab` to move between gaps and `Space` to cycle through available choices for the currently focused gap. No mouse is required.

---

## Key Fields When Creating a Question

### Question Name
A teacher-facing label used in the question bank. Students never see this. Make it descriptive enough to find the question easily later (e.g. *"Grammar – Present Perfect – Gap Fill"*).

### Question Text
This is the passage of text shown to students, with gaps marked using the `[[n]]` syntax (double square brackets containing a number). Each number corresponds to a numbered choice in the Choices section below.

**Syntax rules:**
- Use `[[1]]`, `[[2]]`, `[[3]]` etc. to place gaps in the text.
- The number inside the brackets must match the number of the correct Choice entry. So `[[1]]` means the answer is whichever word or phrase is entered in Choice 1.
- Numbers do not need to appear in order in the text. You can write `[[3]] sat on the [[1]]` perfectly validly.
- The same number can appear more than once in the text only if that Choice is marked as **Infinite** (see below).
- Gaps can be placed inside lists and tables as well as flowing prose.
- Full rich-text formatting of the surrounding text is supported.

**Example:**
> The `[[1]]` sat on the `[[2]]`. It was a `[[3]]` day.

With Choice 1 = *cat*, Choice 2 = *mat*, Choice 3 = *sunny*.

### Default Mark
The total marks available for the question. All gaps are weighted **equally** — the mark for a correctly filled gap is (Default Mark ÷ total number of gaps). There is no way to weight individual gaps differently. There is also no negative marking: an incorrect gap simply scores zero, it does not deduct marks.

### General Feedback
Shown to all students after the question closes (or after they submit, depending on quiz settings). Best practice is to include a fully worked explanation and the complete correct answer, so students who got it wrong understand why.

---

## The Choices Section

Each choice is a word or phrase that becomes a draggable tile. Choices are numbered (1, 2, 3 …) and the number matches the `[[n]]` placeholder in the question text.

### Adding Extra (Distractor) Choices
You can add more choices than there are gaps. These extra choices are distractors — they appear in the draggable set but do not belong in any gap. This increases difficulty because students cannot simply use elimination.

### Groups
Each choice can be assigned to a **Group** (Group 1, Group 2, Group 3, etc.). Choices within the same group are displayed in the same colour, and a coloured gap will only accept tiles from the matching group. This is useful when:
- Some gaps require nouns and others require verbs, and you want to prevent cross-category drops.
- You want to visually scaffold the task (colour hints reduce cognitive load).
- You are building a matching or classification task using a table layout.

If all choices are in Group 1 (the default), all tiles are the same colour and can be dropped into any gap.

### Infinite (Unlimited Use)
By default, each tile can only be placed once. Once a student drops a tile into a gap, that tile is no longer available from the choice list.

Ticking **Infinite** (sometimes labelled *Unlimited*) on a choice means the tile is never consumed — the student can drag copies of it into as many gaps as needed. Use this when the same word is the correct answer for more than one gap, or when you deliberately want to allow repeated use.

**Example:** *"The `[[1]]` sat on the `[[1]]`."* with Choice 1 = *cat* marked Infinite — both gaps require "cat".

### Shuffle
When **Shuffle** is ticked, the display order of the tiles is randomised each time a student attempts the question. This prevents students from memorising positional patterns between attempts. Shuffling is recommended in most cases.

### Formatting in Choices
Limited HTML formatting is supported inside choice tiles: `<b>`, `<i>`, `<em>`, `<strong>`, `<sub>`, `<sup>`. Full block-level HTML is not supported inside drag tiles. If you need to drag long sentences, the Open University's guidance is: *don't* — instead, assign each sentence a short label and drag the label.

### Blank Drag Items
There is no native "leave this gap empty" option. If the correct answer for a gap is genuinely blank (e.g. testing whether students know that nothing belongs there), the workaround is to create a choice that contains only a non-breaking space (`&nbsp;`). In TinyMCE this can be inserted with one click; in other editors type `&nbsp;` directly. The plugin ignores choices that consist only of regular spaces, so a plain space will not work.

---

## Marking and Scoring

- All gaps carry equal weight.
- Only correctly filled gaps score marks; incorrect gaps score zero (no negative marking).
- If a student leaves a gap empty, it scores zero.
- The question does **not** support alternative correct answers for the same gap natively. If you need to accept multiple valid answers for a single gap, consider the **Gapfill** question type by Marcus Green, which supports alternatives via the `|` operator.

---

## Behaviour Options and Multiple Tries

The question supports all standard Moodle question behaviours. The most nuanced is **Interactive with Multiple Tries**:

- **Penalty per try**: the available mark is reduced by the penalty fraction for each incorrect attempt after the first. For example, with a penalty of 0.3333, a correct answer on the second try earns ≈ 0.6667 of the marks; on the third try, ≈ 0.3334.
- **Hints**: provide one hint per additional try you wish to allow. Two hints = up to three attempts.
- **Clear incorrect responses**: when ticked, incorrect tiles are removed from gaps when the student clicks *Try again*, forcing them to reconsider those answers.
- **Show number of correct responses**: tells students how many gaps they filled correctly without revealing which ones. This is overridden by the equivalent setting in the multiple-tries section of the editing form when using interactive behaviour.
- **Combined feedback**: shown after every try (in interactive mode) and at completion. Use *Correct*, *Partially correct*, and *Incorrect* combined feedback to give appropriate messages.

---

## Accessibility

- The question is fully keyboard accessible without a mouse.
- `Tab` moves focus between gaps.
- `Space` cycles through available choices for the focused gap.
- On mobile and tablet devices the question automatically switches to a tap-to-select, tap-to-place interaction model rather than drag-and-drop.
- Colour groups add a visual cue but should not be the *only* differentiator if accessibility for colour-blind users is a concern — consider also using distinct group labels in the text.

---

## Common Mistakes to Avoid

- **Mismatched numbers**: if `[[3]]` appears in the question text but there is no Choice 3, the gap will render but can never be filled correctly. Always check that every `[[n]]` has a matching numbered choice.
- **Reusing a number without marking Infinite**: if `[[1]]` appears twice in the text and Choice 1 is not marked Infinite, only one instance can ever be filled. Mark the choice as Infinite when the same answer is needed in multiple places.
- **Using only regular spaces in a blank tile**: the plugin strips plain spaces. Use `&nbsp;` for a tile that should appear visually empty.
- **Very long choice text**: drag tiles do not wrap across multiple lines. Keep choices concise. For longer phrases, consider using labels and defining the full text in the question prose.
- **Forgetting distractors**: without distractors, students can fill gaps by elimination rather than knowledge. Add at least one or two plausible but incorrect choices to improve validity.
- **Not testing keyboard navigation**: always preview the question and tab through it before publishing to confirm the experience is accessible.
- **Not providing General Feedback**: students who answer incorrectly need an explanation of the correct answer. Always complete the General Feedback field.
- **Expecting per-gap weighting**: all gaps are equal. If some answers are more important than others, split the question or accept that the mark distribution will be uniform.

---

## Workflow for Creating a Question

1. Select **Drag and Drop into Text** as the question type.
2. Enter a meaningful **Question Name** (not seen by students).
3. Write the **Question Text** in the editor, inserting `[[1]]`, `[[2]]`, etc. where gaps should appear.
4. Set the **Default Mark** to reflect the total marks for the question.
5. In the **Choices** section, enter the correct word or phrase for each number (Choice 1 = the answer for `[[1]]`, and so on).
6. Add extra distractor choices beyond the number of gaps if desired.
7. Assign **Groups** if you want colour-coded categories of answers.
8. Tick **Infinite** on any choice that must be usable more than once.
9. Tick **Shuffle** to randomise the tile display order for students.
10. Complete **General Feedback** with the correct answer and explanation.
11. Set **Combined Feedback** messages for correct, partially correct, and incorrect outcomes.
12. If using Multiple Tries, add **Hints** and configure the penalty and hint behaviour options.
13. **Preview** the question, try filling it correctly and incorrectly, and test keyboard navigation before publishing.

---

## Comparison With Related Question Types

| Feature | Drag and Drop into Text (ddwtos) | Select Missing Words (gapselect) | Gapfill (Marcus Green) |
|---|---|---|---|
| Input method | Drag and drop / tap | Dropdown menu | Free text typing |
| Alternative correct answers | No | No | Yes (`\|` operator) |
| Distractors | Yes | Yes | Yes (comma list) |
| Unlimited use tiles | Yes (Infinite) | N/A | N/A |
| Groups / colour coding | Yes | Yes | No |
| Mobile friendly | Yes (tap mode) | Yes | Yes |
| LaTeX / MathJax in choices | Limited (workaround needed) | Limited | Limited |

Use **Drag and Drop into Text** when you want a visually engaging, interactive gap-fill where students physically place words into a passage. Use **Select Missing Words** for an equivalent task with a simpler dropdown interface. Use **Gapfill** when you need free-text entry or multiple valid answers per gap.