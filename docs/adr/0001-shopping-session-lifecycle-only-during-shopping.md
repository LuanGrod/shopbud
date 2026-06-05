# Persist Only The Shopping Session Lifecycle During Shopping

When a user starts a Shopping Session, the backend creates an `active` session with the Template Snapshot, but it does not persist each marked Shopping Item during the shopping run. The frontend owns navigation, temporary item state, subtotals, total preview, and offline continuation; the backend receives the final Shopping Items only when the session is finished, recalculates the official totals, persists the Shopping Items and Purchase History, and marks the session as `finished`.

## Considered Options

- Persist every item mutation in the backend during the session.
- Keep the whole session frontend-only until finalization.
- Persist only the backend lifecycle and initial Snapshot, then receive final items on finish.

We chose the lifecycle-only backend approach because a Shopping Session is short-lived, usually happens on one device, and must work smoothly offline after it starts. This keeps offline sync simple while still letting the backend enforce session ownership, status, expiration, cancellation, and official totals.

## Consequences

- A Shopping Session must be started online so the backend can create the `active` session and Snapshot.
- Shopping Items are persisted only when the session is finished.
- Offline sync sends a high-level `finish_session` operation with the final items, not every item mutation.
- An `active` Shopping Session older than 24 hours is cancelled automatically.
- If a delayed offline finish arrives after expiration, the backend rejects it and the frontend may discard the local copy.
- If a user starts a new session while an unexpired active session exists, the backend returns the active session instead of creating another one.
