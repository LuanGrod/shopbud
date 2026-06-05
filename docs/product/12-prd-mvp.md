## PRD: MVP Shopbud

### Problem Statement

Pessoas que fazem compras de supermercado regularmente perdem tempo e controle porque suas listas não acompanham a ordem real dos corredores. Isso faz com que elas voltem a setores já percorridos, esqueçam itens, tenham dificuldade para registrar preços e quantidades durante a compra e só descubram o total gasto tarde demais.

O Shopbud precisa entregar um MVP em que o usuário modele a estrutura de supermercados que frequenta, inicie uma compra a partir dessa estrutura, acompanhe gastos em tempo real e preserve o histórico da compra mesmo quando o template original mudar no futuro.

### Solution

O MVP entrega uma PWA com API Laravel em que o usuário autenticado pode criar Templates de supermercados. Cada Template contém Sectors ordenados conforme a rota real do supermercado, e cada Sector contém Products não ordenados.

Ao iniciar uma Shopping Session, o backend cria uma sessão `active` com Snapshot imutável do Template naquele momento. Durante a compra, o frontend opera localmente sobre esse Snapshot: o usuário navega pelos setores, marca Shopping Items com preço e quantidade, adiciona produtos avulsos somente na sessão atual, acompanha subtotais e total temporário, e finaliza a compra enviando ao backend a lista final de itens.

O Template permanece como esqueleto para compras futuras. Alterações no Template não afetam sessões já iniciadas, alterações dentro da sessão não alteram o Template, e o backend calcula os totais oficiais apenas quando a sessão é finalizada.

### User Stories

1. As a visitor, I want to create an account with name, email and password, so that I can manage my own supermarket templates.
2. As a registered user, I want to log in with email and password, so that I can access my private shopping data.
3. As an authenticated user, I want to log out, so that I can end access to my account on the current device.
4. As an authenticated user, I want to see my Templates, so that I can choose which supermarket structure to use.
5. As an authenticated user, I want my Templates ordered by most recently updated by default, so that the supermarket I changed last appears first.
6. As an authenticated user, I want to create a Template with a user-unique name, so that each supermarket is clearly identified.
7. As an authenticated user, I want to rename a Template, so that I can correct or improve the supermarket name.
8. As an authenticated user, I want to delete a Template, so that I can remove supermarket structures I no longer use.
9. As an authenticated user, I want deleting a Template to remove its Sectors and Products, so that obsolete template structure does not remain.
10. As an authenticated user, I want deleting a Template to revoke its Shared Templates, so that active sharing links stop working.
11. As an authenticated user, I want deleting a Template not to delete old Shopping Sessions or Purchase History, so that my past shopping records remain intact.
12. As an authenticated user, I want to open a Template detail view with all Sectors and Products, so that I can edit the full supermarket structure.
13. As an authenticated user, I want to create Sectors inside a Template, so that I can represent supermarket aisles or areas.
14. As an authenticated user, I want a Sector name to be unique inside its Template, so that my supermarket route is not ambiguous.
15. As an authenticated user, I want new Sectors to be added at the end of the Template route, so that creation is simple and predictable.
16. As an authenticated user, I want to rename a Sector, so that I can correct aisle names.
17. As an authenticated user, I want to delete a Sector, so that I can remove areas that no longer exist in the supermarket.
18. As an authenticated user, I want deleting a Sector to delete its Products, so that products do not remain detached from the route.
19. As an authenticated user, I want to reorder Sectors, so that the Template follows the real route I walk through the supermarket.
20. As an authenticated user, I want the backend to normalize Sector order, so that the saved route is always a clean sequence.
21. As an authenticated user, I want to create Products inside a Sector, so that I know what to look for in that aisle.
22. As an authenticated user, I want a Product name to be unique inside its Sector, so that I do not manage duplicate expected items.
23. As an authenticated user, I want to rename a Product, so that I can correct expected item names.
24. As an authenticated user, I want to delete a Product, so that I can remove expected items I no longer buy.
25. As an authenticated user, I want Products listed by creation order, so that there is a stable order without pretending to know exact shelf position.
26. As an authenticated user, I want to start a Shopping Session from a Template, so that I can use a supermarket route during a real purchase.
27. As an authenticated user, I want the system to prevent starting a Shopping Session from a Template without Sectors, so that every purchase has at least one route step.
28. As an authenticated user, I want only one active Shopping Session at a time, so that I do not confuse concurrent shopping runs.
29. As an authenticated user with an active Shopping Session, I want to continue it, so that I do not lose my current purchase.
30. As an authenticated user with an active Shopping Session, I want to discard it and start another, so that I can recover from starting the wrong purchase.
31. As an authenticated user, I want a Shopping Session to be created online with an active backend record and Snapshot, so that the backend can control the session lifecycle.
32. As an authenticated user, I want a Shopping Session to copy the current Template into a Snapshot, so that future Template edits do not alter this purchase.
33. As an authenticated user, I want later Template edits not to affect an active or finished Shopping Session, so that historical purchases remain truthful.
34. As an authenticated user, I want changes made during a Shopping Session not to affect the Template, so that the supermarket skeleton remains intentional.
35. As an authenticated user, I want to navigate to the next Sector, so that I can follow the supermarket route.
36. As an authenticated user, I want to navigate to the previous Sector, so that I can go back if needed.
37. As an authenticated user, I want to jump directly to a Sector, so that I can adapt when I move through the supermarket differently.
38. As an authenticated user, I want to see Products for the current Sector, so that I can focus only on items available in that aisle.
39. As an authenticated user, I want to mark a Shopping Item as picked with price and quantity locally during the session, so that totals update immediately.
40. As an authenticated user, I want price to be required and at least R$ 0,01 when an item is finalized, so that totals are meaningful.
41. As an authenticated user, I want quantity to be required and at least 1 when an item is finalized, so that totals are meaningful.
42. As an authenticated user, I want to unmark a picked Shopping Item locally, so that I can correct mistakes.
43. As an authenticated user, I want to edit price and quantity locally for a picked Shopping Item, so that I can correct values.
44. As an authenticated user, I want to add a product avulso inside the current session Sector, so that I can handle unexpected purchases.
45. As an authenticated user, I want product avulso additions to stay only in the Shopping Session, so that the Template is not changed accidentally.
46. As an authenticated user, I want the system to block creating Sectors during a Shopping Session, so that the route remains the Snapshot created at session start.
47. As an authenticated user, I want to cancel a Shopping Session when the Template was missing a Sector, so that I can fix the Template and start a new session.
48. As an authenticated user, I want to see the subtotal for the current Sector calculated locally, so that I know how much that aisle contributed.
49. As an authenticated user, I want to see the total purchase cost calculated locally in real time, so that I can control spending before checkout.
50. As an authenticated user, I want to finalize a Shopping Session by sending final items to the backend, so that the purchase is closed officially.
51. As an authenticated user, I want the backend to recalculate official subtotals and total on finish, so that persisted totals do not depend on client calculations.
52. As an authenticated user, I want to finalize even when no Products were marked, so that I can close an empty or abandoned purchase intentionally.
53. As an authenticated user, I want to see a final summary with sectors, items, prices, quantities, subtotals and total, so that I can review the purchase.
54. As an authenticated user, I want finalized purchases saved to history, so that I can review past spending.
55. As an authenticated user, I want active sessions older than 24 hours to be cancelled automatically, so that abandoned sessions do not block new purchases.
56. As an authenticated user, I want starting a new session to return my existing valid active session, so that I do not create two concurrent purchases.
57. As an authenticated user, I want to list previous purchases ordered by date, so that I can find recent purchases quickly.
58. As an authenticated user, I want to view purchase details, so that I can inspect what was bought in each Sector.
59. As an authenticated user, I want to delete a historical purchase, so that I can remove records I do not want to keep.
60. As an authenticated user, I want to view spending evolution over time, so that I can understand my shopping costs.
61. As an authenticated user, I want to view spending evolution by Sector, so that I can identify where costs are concentrated.
62. As an authenticated user, I want to share a Template with a temporary code, so that another user can import the same supermarket structure.
63. As an authenticated user, I want shared codes to be based on a Snapshot, so that later Template edits do not alter the shared copy.
64. As an authenticated user, I want shared codes to expire after 24 hours, so that old links do not remain usable forever.
65. As an authenticated user, I want deleting the original Template to revoke active shared codes, so that deleted template structures are no longer exposed.
66. As an authenticated user, I want to import a Template from a valid sharing code, so that I can reuse another user's supermarket structure.
67. As an authenticated user importing a Template, I want a suggested new name when I already have a Template with the same name, so that the import can proceed without conflict.
68. As an authenticated user, I want an imported Template to belong to me independently, so that future changes do not affect the original owner.
69. As an authenticated user, I want to compare two prices by unit, liter, kilogram, milliliter or gram, so that I can choose the better deal.
70. As an authenticated user, I want the comparison to require the same unit for both products, so that the result is valid.
71. As an authenticated user, I want to use an active Shopping Session offline after it was started online, so that the app still works inside a supermarket.
72. As an authenticated user, I want finalization while offline stored as a pending `finish_session`, so that my completed purchase can sync later.
73. As an authenticated user, I want pending finalization synchronized later, so that server data catches up when connection returns.
74. As an authenticated user, I want delayed finalization rejected if the session expired or was cancelled, so that stale local purchases do not become official history.

### Implementation Decisions

- The MVP uses Laravel 13, PHP 8.4, MySQL, Laravel Sanctum, Next.js, TypeScript, Tailwind CSS, IndexedDB and Workbox.
- Existing authentication is part of the MVP baseline: register, login, logout, Sanctum-protected routes and rate limiting.
- The core domain aggregate is Template -> Sector -> Product.
- Template names are unique per user.
- Sector names are unique per Template.
- Product names are unique per Sector.
- Sector order is meaningful and represents the supermarket route.
- Product order is not meaningful; Products are listed by creation order.
- Creating a Sector appends it to the end of the Template route.
- Reordering Sectors is a separate operation. The backend validates that all submitted Sectors belong to the Template and persists a normalized sequence.
- `GET /templates` returns a lightweight list with only `id` and `name`, ordered by `updated_at desc` by default. Sorting by name is allowed.
- `GET /templates/{template}` returns the full Template structure with ordered Sectors and each Sector's Products.
- Template mutations are granular: rename Template, manage Sectors, reorder Sectors, manage Products.
- Deleting a Template cascades to its Sectors, Products and active Shared Templates.
- Deleting a Template does not delete old Shopping Sessions or Purchase History.
- Deleting a Sector cascades to its Products.
- Shopping Session creation requires an existing Template with at least one Sector.
- Only one active Shopping Session is allowed per user.
- Starting a Shopping Session requires the user to be online.
- Starting a Shopping Session creates an `active` backend session with an immutable Snapshot of the Template's Sectors and Products.
- Later Template changes do not affect active or historical Shopping Sessions.
- Session changes do not affect the Template.
- Products added during a Shopping Session are Shopping Items only, inside an existing Snapshot Sector.
- Creating Sectors during a Shopping Session is not allowed.
- If a Sector is missing during a purchase, the user cancels the session, edits the Template and starts a new session.
- During a Shopping Session, the frontend owns navigation, marked items, product avulso additions, subtotals and total preview.
- The backend does not persist Shopping Item mutations during the shopping run.
- Shopping Items carry session-specific purchase data: product name, sector name, price, quantity and extra flag.
- Shopping Items are persisted only when the Shopping Session is finished.
- Purchase totals are recalculated by the backend on finish: subtotal by Sector and total across Sectors.
- Finalizing a Shopping Session creates immutable Purchase History.
- An active Shopping Session expires after 24 hours and is cancelled automatically.
- Starting a new session returns an existing unexpired active session instead of creating another one.
- Shared Templates are temporary code-based access to a Template Snapshot.
- Shared Template codes expire after 24 hours.
- Deleting the original Template revokes active Shared Template codes.
- Importing a Shared Template creates a new independent Template for the importing user.
- Offline support allows continuing a session already started online.
- Sync stores a high-level pending `finish_session` operation when the user finalizes offline.
- Sync rejects pending finalizations for expired or cancelled sessions.
- The price comparison calculator is frontend-only in the MVP.

### API Surface

- Auth: register, login and logout.
- Templates: list, create, show full structure, rename, delete, share and import.
- Sectors: list, create, rename, delete and reorder within a Template.
- Products: list, create, rename and delete within a Sector.
- Shopping: start, get current, finish and cancel.
- History: list, show details, delete and stats/evolution.
- Sync: accept pending high-level offline operations, especially `finish_session`.

### Data Decisions

- User stores name, email and hashed password.
- Template stores owner and name.
- Sector stores Template reference, name and normalized order.
- Product stores Sector reference and name.
- Shared Template stores code, Template reference, Snapshot and expiration.
- Shopping Session stores user, optional Template reference, status, Snapshot and expiration timestamp.
- Shopping Item stores session reference, copied sector name, product name, price, quantity and extra flag.
- Purchase History stores user, copied Template name, finished time, total and sector summary.
- Snapshot fields preserve the relevant structure when a Shopping Session or Shared Template is created.
- Database constraints should enforce ownership relationships, scoped uniqueness and intended cascade/null-on-delete behavior.

### Testing Decisions

- Tests should verify external behavior through HTTP/API seams wherever possible, not internal implementation details.
- Existing auth feature tests are prior art for request/response behavior, validation and protected routes.
- Template tests should cover authentication, ownership, user-scoped uniqueness, lightweight listing, full detail response, rename and delete cascades.
- Sector tests should cover creation at the end of order, template-scoped uniqueness, rename, delete cascade and reorder normalization.
- Product tests should cover sector-scoped uniqueness, create, rename, delete and stable creation-order listing.
- Shopping Session tests should cover starting from a valid Template, blocking Template without Sectors, requiring online start, one active session per user, returning an unexpired active session, expiring old active sessions, Snapshot creation and Snapshot independence from later Template edits.
- Shopping Item tests should cover final payload validation, minimum price, minimum quantity, product avulso only inside an existing Snapshot Sector and persistence only on finish.
- Shopping tests should verify that local session changes never mutate Template Products.
- Finish tests should cover finalizing with marked items, finalizing with no marked items, backend recalculation of official totals, creating Purchase History and closing the active session.
- History tests should cover listing, details, deletion and preservation after Template deletion.
- Shared Template tests should cover code creation, expiration, import, name conflict handling and revocation when the original Template is deleted.
- Calculator tests can be frontend/unit-level because it has no backend dependency.
- Offline/sync tests should cover pending `finish_session`, successful finalization after reconnection and rejection when the session expired or was cancelled.

### Acceptance Criteria

- A user can register, log in, use protected API routes and log out.
- A user can create, rename, list and delete their own Templates.
- A user cannot access or modify another user's Templates, Sectors, Products, Sessions or History.
- Template names cannot duplicate within the same user.
- A user can manage Sectors for a Template, and Sector names cannot duplicate within that Template.
- New Sectors are appended at the end of the route.
- Reordered Sectors are saved as a normalized sequence.
- A user can manage Products for a Sector, and Product names cannot duplicate within that Sector.
- Products are not manually reorderable.
- A user can start a Shopping Session from a Template with at least one Sector.
- Starting a Shopping Session requires online access and captures the current Template structure into a Snapshot.
- Later Template edits do not change the active or finished Shopping Session.
- Products added during Shopping Session do not appear in the Template.
- A user can navigate Sectors and mark/unmark/edit Shopping Items locally during the session.
- Subtotals and totals reflect locally marked Shopping Items during the session.
- A user can finish a Shopping Session by sending final items, and the backend recalculates official totals before creating Purchase History.
- Active sessions older than 24 hours are cancelled automatically.
- Purchase History survives Template deletion.
- Sharing a Template creates a temporary Snapshot code.
- Deleting a Template invalidates active Shared Template codes.
- Importing a valid Shared Template creates an independent Template.
- The app can support a full shopping session offline after it is started online and synchronize a pending finalization later.
- Pending finalization after session expiration is rejected.

### Out of Scope

- Integration with supermarket APIs for price lookup.
- Product suggestions based on previous purchases.
- Barcode scanning.
- Notifications and reminders.
- Native mobile application.
- External report export to PDF or spreadsheet.
- Real-time collaborative mode.
- Voice assistant integration.
- OCR for price labels.
- Password recovery flow with email and temporary token.
- Google login.
- Product ordering inside a Sector.
- Creating Sectors during a Shopping Session.
- Adding products from a Shopping Session back into the Template.
- Starting a Shopping Session offline.
- Persisting every Shopping Item mutation in the backend during the shopping run.

### Further Notes

- The MVP should preserve the domain distinction between Product and Shopping Item. Product is an expected item in a Template Sector; Shopping Item is session-specific and carries purchase details.
- The MVP should preserve the domain distinction between Template and Snapshot. Template is the reusable supermarket structure; Snapshot is the immutable copy used for a Shopping Session or Shared Template.
- The MVP should prioritize simple, direct UI flows for users with low technical familiarity.
- The implementation should keep documentation language aligned with the project glossary.
