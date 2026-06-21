import Link from "next/link";

type ButtonLinkProps = {
  href: string;
  children: React.ReactNode;
  variant?: "primary" | "secondary" | "accent";
};

export function ButtonLink({
  href,
  children,
  variant = "primary",
}: ButtonLinkProps) {
  const variants = {
    primary: "bg-primary text-primary-foreground",
    secondary: "border border-primary bg-surface text-primary",
    accent: "bg-accent text-accent-foreground",
  };

  return (
    <Link
      href={href}
      className={`inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl px-4 text-sm font-semibold transition ${variants[variant]}`}
    >
      {children}
    </Link>
  );
}

type SummaryCardProps = {
  label: string;
  value: string;
};

export function SummaryCard({ label, value }: SummaryCardProps) {
  return (
    <div className="rounded-xl border border-border bg-surface p-4 shadow-[0_2px_8px_rgba(0,0,0,0.08)]">
      <p className="text-sm font-medium text-muted-foreground">{label}</p>
      <p className="mt-2 font-heading text-3xl font-bold leading-none text-foreground">
        {value}
      </p>
    </div>
  );
}

type PlaceholderPanelProps = {
  title: string;
  description: string;
};

export function PlaceholderPanel({ title, description }: PlaceholderPanelProps) {
  return (
    <section className="rounded-xl border border-border bg-secondary p-4">
      <h2 className="font-heading text-lg font-semibold text-secondary-foreground">
        {title}
      </h2>
      <p className="mt-2 text-sm leading-5 text-muted-foreground">
        {description}
      </p>
    </section>
  );
}
