import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";
import { motion, HTMLMotionProps } from "motion/react";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

interface GlassCardProps extends HTMLMotionProps<"div"> {
  children: React.ReactNode;
  className?: string;
  hoverEffect?: boolean;
}

export function GlassCard({ children, className, hoverEffect = false, ...props }: GlassCardProps) {
  return (
    <motion.div
      className={cn(
              "rounded-3xl border border-white/20 bg-white/20 shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] backdrop-blur-xl dark:border-white/10 dark:bg-gray-900/40 dark:shadow-[0_8px_32px_0_rgba(0,0,0,0.3)]",
              hoverEffect && "transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_15px_35px_0_rgba(31,38,135,0.15)] dark:hover:shadow-[0_15px_35px_0_rgba(0,0,0,0.5)]",
              className
            )}
      {...props}
    >
      {children}
    </motion.div>
  );
}
