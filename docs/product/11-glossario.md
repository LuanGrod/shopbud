## 11. Glossário

| Termo | Definição |
| --- | --- |
| **Template** | Estrutura reutilizável de um supermercado, pertencente a um usuário, contendo nome único por usuário e setores ordenados conforme a rota real de compra. |
| **Setor** | Área ou corredor de um supermercado dentro de um template. O nome é único dentro do template e a ordem reflete o caminho real percorrido pelo usuário. |
| **Produto** | Item não ordenado que o usuário espera encontrar dentro de um setor de um template. O nome é único dentro do setor e não carrega preço ou quantidade. |
| **Item da Compra** | Item específico de uma sessão de compra, derivado de um produto do template ou adicionado durante a sessão, com preço, quantidade e dados daquela compra. |
| **Sessão de Compra** | Compra concreta criada a partir do estado atual de um template, independente de alterações futuras nesse template. Só pode existir uma sessão ativa por vez. |
| **Snapshot** | Cópia imutável dos setores e produtos de um template no momento em que uma sessão de compra ou compartilhamento é criado. |
| **Produto Avulso** | Item da compra adicionado durante uma sessão que não existia no snapshot inicial. Ele pertence apenas à sessão atual e não altera o template original. |
| **Produto Marcado** | Item da compra que o usuário indicou ter pego durante a compra, com preço e quantidade informados. Contribui para o cálculo dos totais. |
| **Subtotal do Setor** | Soma dos valores (preço × quantidade) de todos os produtos marcados em um setor específico durante uma sessão de compra. |
| **Total da Compra** | Soma de todos os subtotais de todos os setores. Representa o valor total gasto na compra. |
| **Histórico de Compras** | Registro permanente de todas as compras finalizadas pelo usuário, contendo data, template usado, produtos comprados com preços e quantidades, subtotais por setor e total geral. |
| **Template Compartilhado** | Acesso temporário ao snapshot de um template por código. O acesso expira em 24 horas e é revogado se o template original for excluído. |
| **Código de Compartilhamento** | Código único e temporário gerado ao compartilhar um template. Permite que outro usuário importe uma cópia do snapshot enquanto o código estiver válido. |
| **Sincronização** | Processo de enviar uma finalização pendente de sessão ao servidor quando a conexão é restabelecida. |
| **Offline-first** | Estratégia em que uma sessão de compra já iniciada online continua funcionando localmente, usando armazenamento local (IndexedDB), e sincronizando a finalização quando possível. |
| **PWA (Progressive Web App)** | Aplicação web que utiliza tecnologias modernas (Service Worker, manifest) para oferecer experiência similar a um app nativo, incluindo instalação no dispositivo, funcionamento offline e acesso pela home screen. |
| **Service Worker** | Script que roda em segundo plano no navegador, permitindo funcionalidades como cache de recursos, interceptação de requisições e funcionamento offline. |
| **IndexedDB** | Banco de dados local do navegador usado para armazenar dados estruturados no dispositivo do usuário, permitindo funcionamento offline. |
| **FAB (Floating Action Button)** | Botão circular flutuante posicionado sobre o conteúdo da tela, geralmente no canto inferior direito, usado para ações principais ou acesso rápido a funcionalidades. |
| **Drag-and-drop** | Interação onde o usuário arrasta um setor e solta em outra posição para reordenar a rota do supermercado. |
| **Token Sanctum** | Token de API emitido pelo Laravel Sanctum para autenticar requisições do usuário. |
| **Endpoint** | URL específica da API que realiza uma operação. Ex: `POST /api/templates` cria um novo template. |
| **Migration** | Arquivo de código que define alterações na estrutura do banco de dados (criar tabelas, adicionar colunas). Permite versionar e replicar a estrutura do banco. |
