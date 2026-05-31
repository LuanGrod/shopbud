## 11. Glossário

| Termo | Definição |
| --- | --- |
| **Template** | Lista base de compras criada pelo usuário, contendo setores e produtos organizados. Funciona como um modelo reutilizável para iniciar sessões de compra. Ex: "Compra Mensal - Assaí". |
| **Setor** | Subdivisão de um template que representa uma área ou corredor do supermercado. Os setores são ordenados pelo usuário para refletir o caminho real que ele percorre no supermercado. Ex: "Limpeza", "Hortifruti", "Padaria". |
| **Produto** | Item individual dentro de um setor que o usuário pretende comprar. No template, é apenas o nome do produto. Durante a compra, inclui preço e quantidade. |
| **Sessão de Compra** | Instância ativa de uma compra em andamento. É criada a partir de um template e permite ao usuário navegar pelos setores, marcar produtos e acompanhar gastos em tempo real. Só pode existir uma sessão ativa por vez. |
| **Snapshot** | Cópia congelada dos dados de um template em um determinado momento. Usado para preservar o estado original quando uma sessão de compra é iniciada ou quando um template é compartilhado, garantindo que alterações futuras no template não afetem dados históricos. |
| **Produto Avulso** | Produto adicionado durante uma sessão de compra que não existia no template original. Pode ser adicionado apenas na compra atual ou também ser salvo no template para compras futuras. |
| **Produto Marcado** | Produto que o usuário indicou ter pego durante a compra, com preço e quantidade informados. Contribui para o cálculo dos totais. |
| **Subtotal do Setor** | Soma dos valores (preço × quantidade) de todos os produtos marcados em um setor específico durante uma sessão de compra. |
| **Total da Compra** | Soma de todos os subtotais de todos os setores. Representa o valor total gasto na compra. |
| **Histórico de Compras** | Registro permanente de todas as compras finalizadas pelo usuário, contendo data, template usado, produtos comprados com preços e quantidades, subtotais por setor e total geral. |
| **Código de Compartilhamento** | Código único e temporário (válido por 24 horas) gerado ao compartilhar um template. Permite que outro usuário importe uma cópia do template usando apenas esse código. |
| **Sincronização** | Processo de enviar dados criados ou modificados offline para o servidor quando a conexão é restabelecida, e resolver possíveis conflitos entre dados locais e remotos. |
| **Offline-first** | Estratégia de desenvolvimento onde o app é projetado para funcionar primariamente offline, usando armazenamento local (IndexedDB), e sincronizando com o servidor quando possível. |
| **PWA (Progressive Web App)** | Aplicação web que utiliza tecnologias modernas (Service Worker, manifest) para oferecer experiência similar a um app nativo, incluindo instalação no dispositivo, funcionamento offline e acesso pela home screen. |
| **Service Worker** | Script que roda em segundo plano no navegador, permitindo funcionalidades como cache de recursos, interceptação de requisições e funcionamento offline. |
| **IndexedDB** | Banco de dados local do navegador usado para armazenar dados estruturados no dispositivo do usuário, permitindo funcionamento offline. |
| **FAB (Floating Action Button)** | Botão circular flutuante posicionado sobre o conteúdo da tela, geralmente no canto inferior direito, usado para ações principais ou acesso rápido a funcionalidades. |
| **Drag-and-drop** | Interação onde o usuário arrasta um elemento (setor ou produto) e solta em outra posição para reordenar a lista. |
| **OAuth** | Protocolo de autenticação que permite login usando contas de terceiros (como Google) sem compartilhar a senha do usuário com o aplicativo. |
| **JWT (JSON Web Token)** | Formato de token usado para autenticação na API. Contém informações do usuário de forma criptografada e tem tempo de expiração definido. |
| **Endpoint** | URL específica da API que realiza uma operação. Ex: `POST /api/templates` cria um novo template. |
| **Migration** | Arquivo de código que define alterações na estrutura do banco de dados (criar tabelas, adicionar colunas). Permite versionar e replicar a estrutura do banco. |
