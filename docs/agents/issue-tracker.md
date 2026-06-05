# Issue Tracker: Local Markdown

Issues and PRDs for this project live as markdown files under `.scratch/` at the project root.

The Laravel agent is normally run from `backend/`, so paths may appear as `../.scratch/` from the agent's working directory.

## Conventions

- One feature per directory: `.scratch/<feature-slug>/`
- The PRD is `.scratch/<feature-slug>/PRD.md`
- Implementation issues are `.scratch/<feature-slug>/issues/<NN>-<slug>.md`, numbered from `01`
- Triage state is recorded as a `Status:` line near the top of each issue file
- Comments and conversation history append to the bottom of the file under a `## Comments` heading

## When A Skill Says "Publish To The Issue Tracker"

Create a new file under `.scratch/<feature-slug>/`, creating the directory if needed.

For the MVP PRD at `docs/product/12-prd-mvp.md`, use `.scratch/mvp/` as the feature directory unless the user asks for another name.

## When A Skill Says "Fetch The Relevant Ticket"

Read the file at the referenced path. The user will normally pass the path or issue number directly.
