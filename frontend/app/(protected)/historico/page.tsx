import { PageHeader } from "@/app/_components/page-header";
import { PlaceholderPanel } from "@/app/_components/ui";

export default function HistoricoPage() {
  return (
    <div className="space-y-6">
      <PageHeader
        title="Histórico"
        subtitle="Consulte compras finalizadas e acompanhe os gastos."
      />

      <PlaceholderPanel
        title="Nenhuma compra finalizada"
        description="A listagem do histórico será conectada depois que o fluxo de compra estiver pronto."
      />
    </div>
  );
}
