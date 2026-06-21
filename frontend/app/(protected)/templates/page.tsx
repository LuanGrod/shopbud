import { PlusIcon, UploadSimpleIcon } from "@phosphor-icons/react/ssr";
import { PageHeader } from "@/app/_components/page-header";
import { ButtonLink, PlaceholderPanel } from "@/app/_components/ui";

export default function TemplatesPage() {
  return (
    <div className="space-y-6">
      <PageHeader
        title="Meus Templates"
        subtitle="Crie rotas de supermercado por setores e produtos."
      />

      <div className="grid grid-cols-2 gap-3">
        <ButtonLink href="/templates" variant="primary">
          <PlusIcon aria-hidden="true" size={20} weight="bold" />
          Criar Template
        </ButtonLink>
        <ButtonLink href="/templates" variant="secondary">
          <UploadSimpleIcon aria-hidden="true" size={20} weight="bold" />
          Importar
        </ButtonLink>
      </div>

      <PlaceholderPanel
        title="Nenhum template ainda"
        description="A listagem completa de templates será conectada à API na próxima etapa."
      />
    </div>
  );
}
