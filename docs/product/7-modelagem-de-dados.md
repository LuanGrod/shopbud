## 7. Modelagem de Dados

### 7.1 Entidades

**User (Usuário)**

| Campo      | Tipo              | Descrição                     |
| ---------- | ----------------- | ----------------------------- |
| id         | bigint            | Identificador único           |
| name       | string            | Nome do usuário               |
| email      | string            | E-mail (único)                |
| password   | string            | Senha hasheada                |
| created_at | datetime          | Data de criação               |
| updated_at | datetime          | Data de atualização           |

---

**Template**

| Campo      | Tipo      | Descrição                  |
| ---------- | --------- | -------------------------- |
| id         | bigint    | Identificador único        |
| user_id    | bigint (FK) | Referência ao usuário dono |
| name       | string    | Nome único por usuário     |
| created_at | datetime  | Data de criação            |
| updated_at | datetime  | Data de atualização        |

---

**Sector (Setor)**

| Campo       | Tipo      | Descrição              |
| ----------- | --------- | ---------------------- |
| id          | bigint    | Identificador único    |
| template_id | bigint (FK) | Referência ao template |
| name        | string    | Nome único por template |
| order       | integer   | Posição normalizada na ordem do template |
| created_at  | datetime  | Data de criação        |
| updated_at  | datetime  | Data de atualização    |

---

**Product (Produto)**

| Campo      | Tipo      | Descrição           |
| ---------- | --------- | ------------------- |
| id         | bigint    | Identificador único |
| sector_id  | bigint (FK) | Referência ao setor |
| name       | string    | Nome único por setor |
| created_at | datetime  | Data de criação     |
| updated_at | datetime  | Data de atualização |

---

**SharedTemplate (Template Compartilhado)**

| Campo                | Tipo      | Descrição                                        |
| -------------------- | --------- | ------------------------------------------------ |
| id                   | bigint    | Identificador único                              |
| code                 | string    | Código único de compartilhamento                 |
| template_id          | bigint (FK) | Referência ao template original                  |
| snapshot             | JSON      | Cópia congelada do template (setores e produtos) |
| expires_at           | datetime  | Data de expiração (24h após criação)             |
| created_at           | datetime  | Data de criação                                  |

---

**ShoppingSession (Sessão de Compra)**

| Campo       | Tipo      | Descrição                                        |
| ----------- | --------- | ------------------------------------------------ |
| id          | bigint    | Identificador único                              |
| user_id     | bigint (FK) | Referência ao usuário                            |
| template_id | bigint (FK, nullable) | Referência ao template usado como base, preservando a sessão se o template for excluído |
| status      | enum      | Status da sessão (active, finished, cancelled)   |
| snapshot    | JSON      | Cópia dos setores e produtos no início da sessão |
| expires_at  | datetime  | Data/hora limite para finalizar a sessão active   |
| created_at  | datetime  | Data de criação                                  |
| updated_at  | datetime  | Data de atualização                              |

---

**ShoppingItem (Item da Compra)**

| Campo        | Tipo      | Descrição                                          |
| ------------ | --------- | -------------------------------------------------- |
| id           | bigint    | Identificador único                                |
| session_id   | bigint (FK) | Referência à sessão de compra                      |
| sector_name  | string    | Nome do setor (copiado do snapshot)                |
| product_name | string    | Nome do produto                                    |
| price        | decimal   | Preço unitário                                     |
| quantity     | integer   | Quantidade                                         |
| is_extra     | boolean   | Se é um produto avulso adicionado durante a compra |
| created_at   | datetime  | Data de criação                                    |
| updated_at   | datetime  | Data de atualização                                |

---

**PurchaseHistory (Histórico de Compra)**

| Campo           | Tipo      | Descrição                                   |
| --------------- | --------- | ------------------------------------------- |
| id              | bigint    | Identificador único                         |
| user_id         | bigint (FK) | Referência ao usuário                       |
| template_name   | string    | Nome do template usado (copiado)            |
| finished_at     | datetime  | Data/hora de finalização                    |
| total           | decimal   | Valor total da compra                       |
| sectors_summary | JSON      | Resumo por setor (nome, subtotal, produtos) |
| created_at      | datetime  | Data de criação                             |

---

### 7.2 Relacionamentos

`User (1) ────── (N) Template`

`Template (1) ────── (N) Sector`

`Sector (1) ────── (N) Product`

`Template (1) ────── (N) SharedTemplate`

`User (1) ────── (N) ShoppingSession`

`Template (0..1) ────── (N) ShoppingSession`

`ShoppingSession (1) ────── (N) ShoppingItem`

`User (1) ────── (N) PurchaseHistory`

---

### 7.3 Considerações

- **Snapshots em JSON:** Usados em SharedTemplate, ShoppingSession e PurchaseHistory para preservar o estado exato no momento da ação, independente de alterações futuras nos templates originais.
- **Desnormalização intencional:** Campos como `template_name` e `sector_name` são copiados em vez de referenciados para manter o histórico íntegro mesmo se o template original for editado ou excluído.
- **IndexedDB (offline):** O frontend mantém localmente a sessão já iniciada, o estado temporário de itens marcados e uma fila de operações pendentes, especialmente `finish_session`.
- **Unicidades:** `templates` deve ser único por `user_id + name`, `sectors` por `template_id + name`, `sectors` por `template_id + order`, e `products` por `sector_id + name`.
- **Deleções:** Excluir um template remove setores, produtos e compartilhamentos ativos; sessões e histórico permanecem preservados por snapshot. Excluir um setor remove seus produtos.
- **Sessão ativa:** O backend persiste a ShoppingSession e seu snapshot ao iniciar a compra, mas ShoppingItems só são persistidos quando a sessão é finalizada.
- **Expiração:** Uma ShoppingSession `active` expira em 24 horas e deve ser cancelada antes de iniciar uma nova sessão ou aceitar uma finalização.
