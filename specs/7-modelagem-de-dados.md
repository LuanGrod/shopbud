## 7. Modelagem de Dados

### 7.1 Entidades

**User (Usuário)**

| Campo      | Tipo              | Descrição                     |
| ---------- | ----------------- | ----------------------------- |
| id         | UUID              | Identificador único           |
| name       | string            | Nome do usuário               |
| email      | string            | E-mail (único)                |
| password   | string            | Senha hasheada                |
| google_id  | string (nullable) | ID do Google para login OAuth |
| created_at | datetime          | Data de criação               |
| updated_at | datetime          | Data de atualização           |

---

**Template**

| Campo      | Tipo      | Descrição                  |
| ---------- | --------- | -------------------------- |
| id         | UUID      | Identificador único        |
| user_id    | UUID (FK) | Referência ao usuário dono |
| name       | string    | Nome do template           |
| created_at | datetime  | Data de criação            |
| updated_at | datetime  | Data de atualização        |

---

**Sector (Setor)**

| Campo       | Tipo      | Descrição              |
| ----------- | --------- | ---------------------- |
| id          | UUID      | Identificador único    |
| template_id | UUID (FK) | Referência ao template |
| name        | string    | Nome do setor          |
| order       | integer   | Posição na ordenação   |
| created_at  | datetime  | Data de criação        |
| updated_at  | datetime  | Data de atualização    |

---

**Product (Produto)**

| Campo      | Tipo      | Descrição           |
| ---------- | --------- | ------------------- |
| id         | UUID      | Identificador único |
| sector_id  | UUID (FK) | Referência ao setor |
| name       | string    | Nome do produto     |
| created_at | datetime  | Data de criação     |
| updated_at | datetime  | Data de atualização |

---

**SharedTemplate (Template Compartilhado)**

| Campo                | Tipo      | Descrição                                        |
| -------------------- | --------- | ------------------------------------------------ |
| id                   | UUID      | Identificador único                              |
| code                 | string    | Código único de compartilhamento                 |
| original_template_id | UUID (FK) | Referência ao template original                  |
| snapshot             | JSON      | Cópia congelada do template (setores e produtos) |
| expires_at           | datetime  | Data de expiração (24h após criação)             |
| created_at           | datetime  | Data de criação                                  |

---

**ShoppingSession (Sessão de Compra)**

| Campo       | Tipo      | Descrição                                        |
| ----------- | --------- | ------------------------------------------------ |
| id          | UUID      | Identificador único                              |
| user_id     | UUID (FK) | Referência ao usuário                            |
| template_id | UUID (FK) | Referência ao template usado como base           |
| status      | enum      | Status da sessão (active, finished, cancelled)   |
| snapshot    | JSON      | Cópia dos setores e produtos no início da sessão |
| created_at  | datetime  | Data de criação                                  |
| updated_at  | datetime  | Data de atualização                              |

---

**ShoppingItem (Item da Compra)**

| Campo        | Tipo      | Descrição                                          |
| ------------ | --------- | -------------------------------------------------- |
| id           | UUID      | Identificador único                                |
| session_id   | UUID (FK) | Referência à sessão de compra                      |
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
| id              | UUID      | Identificador único                         |
| user_id         | UUID (FK) | Referência ao usuário                       |
| template_name   | string    | Nome do template usado (copiado)            |
| finished_at     | datetime  | Data/hora de finalização                    |
| total           | decimal   | Valor total da compra                       |
| sectors_summary | JSON      | Resumo por setor (nome, subtotal, produtos) |
| created_at      | datetime  | Data de criação                             |

---

### 7.2 Relacionamentos

`User (1) ────── (N) Template Template (1) ────── (N) Sector Sector (1) ────── (N) Product Template (1) ────── (N) SharedTemplate User (1) ────── (N) ShoppingSession ShoppingSession (1) ────── (N) ShoppingItem User (1) ────── (N) PurchaseHistory`

---

### 7.3 Considerações

- **Snapshots em JSON:** Usados em SharedTemplate, ShoppingSession e PurchaseHistory para preservar o estado exato no momento da ação, independente de alterações futuras nos templates originais.
- **Desnormalização intencional:** Campos como `template_name` e `sector_name` são copiados em vez de referenciados para manter o histórico íntegro mesmo se o template original for editado ou excluído.
- **IndexedDB (offline):** As entidades ShoppingSession e ShoppingItem serão replicadas localmente para funcionamento offline, com um campo adicional `sync_status` (synced, pending) para controle de sincronização.
