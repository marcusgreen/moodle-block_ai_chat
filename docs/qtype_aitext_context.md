# AI Chat Block: Context for the AI Text Question Type

## Overview

The **AI Text** question type (`qtype_aitext`) is a Moodle question type that uses an external AI/LLM to evaluate a student's free-text response. The teacher writes a prompt that is sent to the AI together with the student's response. The AI then returns feedback and, optionally, a mark. It is particularly well-suited to language learning, creative writing, short-answer reasoning, and any task where open-ended responses need qualitative evaluation at scale.

The plugin source is available at: https://github.com/marcusgreen/moodle-qtype_aitext

---

## Key Fields When Creating a Question

### Question Text
This is the instruction or task that the student sees. It should be written in plain, clear language. Example: *"Write a sentence asking someone to phone you on the following day."*

### Response Format
Controls how the student enters their answer. The `editor` option provides a rich-text box. A plain textarea is also available. The number of visible lines can be set with **Response field lines** (e.g. 5 lines for short answers, more for essays).

### Word Limits
Optional minimum and maximum word counts can be enforced. Leave blank if no word limit is required.

### AI Prompt (`aiprompt`)
This is the core instruction sent to the AI. It tells the AI *what to do* with the student's response. The special placeholder `{{response}}` is replaced at runtime with the actual text the student submitted.

**Tips for writing a good AI prompt:**
- Be explicit about what the AI should evaluate (grammar, factual accuracy, creativity, argument structure, etc.).
- Tell the AI what to do in edge cases, for example: *"Stop processing if the response is nonsense."*
- Keep the instruction focused — one clear task produces more consistent marking than several vague ones.
- Use `{{response}}` exactly as shown (double curly braces) so the plugin can substitute the student's answer correctly.

**Example prompt:**
> Evaluate the grammar of `{{response}}` and confirm if it is a valid request for someone to phone you on the following day. Explain any grammar errors. Stop processing if it is nonsense.

### Mark Scheme (`markscheme`)
A separate instruction, also sent to the AI, that explains *how to assign marks*. Keep this distinct from the AI prompt so that evaluation logic and scoring logic are easy to maintain independently.

**Tips for writing a good mark scheme:**
- State the maximum mark and what it represents.
- Describe exactly which errors or qualities should increase or decrease the mark.
- Specify the output format for the mark if required, for example: *"Emphasise the marks part"* or *"Return the mark on its own line."*
- Include instructions for zero-mark situations (nonsense, off-topic, blank).
- HTML formatting instructions can be included, e.g. *"Do HTML formatting of the content."*

**Example mark scheme:**
> Deduct a point from the score down to 0 if there is a spelling or grammar mistake. Comment on why any deduction was made. Give no marks if the response is nonsense. Do HTML formatting of the content. Emphasise the marks part.

### Default Grade
The maximum number of marks available for the question (e.g. 2). The AI prompt and mark scheme should be consistent with this value.

### Grader Info
An internal teacher-facing note (not shown to students) that can document the rationale behind the prompt and mark scheme, or record earlier drafts. It supports `{{response}}` substitution as well, making it useful for reviewing how the prompt evolved.

### Response Template
Optional pre-filled text shown to students when they open the response box. Useful for scaffolding (e.g. sentence starters).

### General Feedback
Shown to all students after the question closes, regardless of their score. Useful for a model answer or general commentary.

### AI Model (`model`)
The identifier of the LLM to use (e.g. `llama3-70b-8192`, `gpt-4o`, etc.). The available models depend on how the plugin has been configured by the site administrator. Larger, more capable models generally produce more nuanced feedback but may cost more or be slower.

### Spell Check
A toggle that enables browser-level spell checking in the student response field.

---

## Sample Responses

Sample responses can be attached to a question. These serve two purposes:

1. **Testing** — they let teachers preview how the AI will respond to a range of answers (correct, partially correct, nonsense, borderline) before the question goes live.
2. **Consistency checking** — running the same sample responses after editing the prompt helps confirm the AI's behaviour has not changed unexpectedly.

Good practice is to include at least:
- A clearly correct response
- A clearly incorrect or nonsense response
- One or two borderline responses that test the edges of the mark scheme

---

## The `{{response}}` Placeholder

`{{response}}` is the only template variable currently supported. It is replaced with the student's verbatim submitted text before the combined prompt is sent to the AI. Always include it in the AI Prompt field, otherwise the AI receives no student text to evaluate.

---

## Workflow for Creating a Question

1. Select **AI Text** as the question type in the Moodle question bank.
2. Enter a clear **Question Text** that tells the student what to write.
3. Set the **Default Grade** to reflect the total marks available.
4. Write the **AI Prompt**, including `{{response}}` where the student's answer should appear.
5. Write the **Mark Scheme** describing how marks should be awarded or deducted.
6. Optionally set **word limits**, a **response template**, and **general feedback**.
7. Choose the **AI Model** appropriate for the task.
8. Add several **Sample Responses** covering the range of expected student answers and preview the AI's feedback for each.
9. Save and test the question in a quiz before releasing it to students.

---

## Common Mistakes to Avoid

- **Omitting `{{response}}`** from the AI Prompt — the AI will have no student text to grade.
- **Combining evaluation and marking in a single vague instruction** — keep the AI Prompt (what to assess) separate from the Mark Scheme (how to score).
- **Not handling edge cases** — always tell the AI what to do if the response is blank, off-topic, or nonsense.
- **Mismatch between Default Grade and Mark Scheme** — if the question is worth 2 marks, the mark scheme should reference a maximum of 2.
- **Overly complex prompts** — LLMs perform better with clear, single-focus instructions. If the task is complex, break it into clear numbered criteria in the mark scheme.
- **Not testing with sample responses** — always run a range of sample answers through the AI before deploying the question to students.