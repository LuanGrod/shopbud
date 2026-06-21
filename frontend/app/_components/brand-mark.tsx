import Image from "next/image";
import wordmark from "@/public/wordmark.svg";

type BrandMarkProps = {
  className?: string;
  priority?: boolean;
};

export function BrandMark({ className, priority = false }: BrandMarkProps) {
  return (
    <Image
      src={wordmark}
      alt="ShopBud"
      className={className}
      priority={priority}
    />
  );
}
