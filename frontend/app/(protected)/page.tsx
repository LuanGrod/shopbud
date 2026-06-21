import { GearSixIcon } from "@phosphor-icons/react/ssr";
import { ButtonLink, SummaryCard } from "@/app/_components/ui";
import { PageHeader } from "@/app/_components/page-header";

const summaryItems = [
  { label: "Templates", value: "0" },
  { label: "Setores", value: "0" },
  { label: "Produtos", value: "0" },
];

export default function HomePage() {
  return (
    <div className="space-y-6">
      <PageHeader
        title="Início"
        subtitle="Organize o mercado antes de sair de casa."
        action={
          <ButtonLink href="/configuracoes" variant="secondary">
            <GearSixIcon aria-hidden="true" size={20} weight="bold" />
            <span className="sr-only">Configurações</span>
          </ButtonLink>
        }
      />

      <section className="grid grid-cols-3 gap-3">
        {summaryItems.map((item) => (
          <SummaryCard key={item.label} label={item.label} value={item.value} />
        ))}
      </section>

      <section className="space-y-3">
        <ButtonLink href="/compra" variant="primary">
          Iniciar Compra
        </ButtonLink>
        <div className="grid grid-cols-2 gap-3">
          <ButtonLink href="/templates" variant="secondary">
            Ver Templates
          </ButtonLink>
          <ButtonLink href="/templates" variant="accent">
            Criar Template
          </ButtonLink>
          <ButtonLink href="/historico" variant="secondary">
            Histórico
          </ButtonLink>
          <ButtonLink href="/configuracoes" variant="secondary">
            Ajustes
          </ButtonLink>
        </div>
      </section>
    </div>
  );
}
