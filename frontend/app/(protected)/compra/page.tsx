import { BasketIcon } from "@phosphor-icons/react/ssr";
import { PageHeader } from "@/app/_components/page-header";
import { ButtonLink, PlaceholderPanel } from "@/app/_components/ui";

export default function CompraPage() {
  return (
    <div className="space-y-6">
      <PageHeader
        title="Compra"
        subtitle="Inicie ou continue uma sessão de supermercado."
      />

      <section className="rounded-xl border border-border bg-surface p-4 shadow-[0_2px_8px_rgba(0,0,0,0.08)]">
        <BasketIcon
          aria-hidden="true"
          size={32}
          weight="bold"
          className="text-primary"
        />
        <h2 className="mt-3 font-heading text-xl font-semibold text-foreground">
          Nenhuma compra em andamento
        </h2>
        <p className="mt-2 text-sm leading-5 text-muted-foreground">
          A seleção de template e a sessão de compra entram na próxima fatia.
        </p>
        <div className="mt-4">
          <ButtonLink href="/templates" variant="primary">
            Escolher Template
          </ButtonLink>
        </div>
      </section>

      <PlaceholderPanel
        title="Totais da compra"
        description="A bottom bar de totais será adicionada dentro do fluxo de sessão, separada da navegação principal."
      />
    </div>
  );
}
