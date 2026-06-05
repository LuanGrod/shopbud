# Domain Docs

This is a single-context fullstack product. The agent may run from `backend/` for Laravel MCP support, but the product domain documents live at the project root.

## Before Exploring Product Behavior

Read these project-level files when work touches product behavior, API contracts, frontend flows, offline behavior, or cross-service architecture:

- `CONTEXT.md` at the project root, available as `../CONTEXT.md` from `backend/`
- `docs/product/` at the project root, available as `../docs/product/` from `backend/`
- `docs/product/12-prd-mvp.md` for the MVP PRD
- `docs/adr/` if ADRs exist

If an ADR directory or ADR file does not exist, proceed silently.

## File Structure

```txt
shopbud/
├── CONTEXT.md
├── docs/
│   ├── product/
│   │   └── 12-prd-mvp.md
│   ├── agents/
│   └── adr/
├── backend/
│   ├── AGENTS.md
│   └── CLAUDE.md
└── frontend/
```

The Laravel agent usually runs from `backend/`, so project-level paths are referenced with `../`.

## Use The Glossary's Vocabulary

When issue titles, implementation plans, tests, or reviews name a domain concept, use the term defined in `CONTEXT.md`.

Important terms include `Template`, `Sector`, `Product`, `Shopping Item`, `Shopping Session`, `Snapshot`, and `Shared Template`.

Do not drift to avoided synonyms unless the user is explicitly changing the glossary.
