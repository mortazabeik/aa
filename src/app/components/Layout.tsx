import { Link, Outlet, useLocation } from "react-router";
import { useTheme } from "../hooks/useTheme";
import {
  Moon,
  Sun,
  Monitor,
  Home as HomeIcon,
  Search as SearchIcon,
  PlusSquare,
  Image as ImageIcon,
  Settings,
} from "lucide-react";
import { motion } from "motion/react";
import { GlassCard } from "./GlassCard";

export function Layout() {
  const { theme, setTheme } = useTheme();
  const location = useLocation();

  const navLinks = [
    { name: "صفحه اصلی", path: "/", icon: HomeIcon },
    { name: "جستجو", path: "/search", icon: SearchIcon },
    { name: "ارسال آثار", path: "/submit", icon: PlusSquare },
  ];

  return (
    <div
      dir="rtl"
      className="min-h-screen bg-[#f3f4f6] dark:bg-[#0f172a] text-slate-800 dark:text-slate-200 transition-colors duration-300 font-sans overflow-hidden relative"
    >
      {/* Background decoration for glassmorphism effect */}
      <div className="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        <div className="absolute top-[-10%] -right-[10%] w-[50%] h-[50%] rounded-full bg-blue-400/20 dark:bg-blue-600/20 blur-[120px]" />
        <div className="absolute bottom-[-10%] -left-[10%] w-[50%] h-[50%] rounded-full bg-emerald-400/20 dark:bg-emerald-600/20 blur-[120px]" />
      </div>
      <div className="w-full h-15"></div>
      <header className="fixed top-0 z-50 w-full md:px-4 md:py-4 items-center flex justify-center">
        <GlassCard className="flex items-center md:justify-center h-16 gap-1 md:w-fit rounded-none md:rounded-full pl-2.5 pr-1.5 w-full justify-between">
          <nav className="flex-shrink-0 p-1.5 pl-3 rounded-full flex items-center min-w-0">
            <img src="/logo.png" className="h-8 sm:h-10 object-contain" alt="Logo" />
          </nav>

          <nav className="hidden md:flex items-center gap-2 p-1.5 rounded-full">
            {navLinks.map((link) => {
              const Icon = link.icon;
              const isActive = location.pathname === link.path;
              return (
                <Link
                  key={link.path}
                  to={link.path}
                  className={`relative flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 ${
                    isActive
                      ? "text-blue-700 dark:text-blue-300"
                      : "text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-gray-700/50"
                  }`}
                >
                  {isActive && (
                    <motion.div
                      layoutId="activeTab"
                      className="absolute inset-0 bg-white/40 dark:bg-gray-700/40 rounded-full shadow-sm border border-gray-200/50 dark:border-gray-600/50"
                      transition={{
                        type: "spring",
                        stiffness: 400,
                        damping: 30,
                      }}
                    />
                  )}
                  <span className="relative z-10 flex items-center gap-2">
                    <Icon
                      size={18}
                      className={
                        isActive
                          ? "text-blue-600 dark:text-blue-500"
                          : ""
                      }
                    />
                    {link.name}
                  </span>
                </Link>
              );
            })}
          </nav>

          <div className="flex items-center gap-2 flex-shrink-0">
            {/* Desktop/Tablet Theme Toggle */}
            <div className="hidden ls:flex bg-white/20 dark:bg-gray-700/40 rounded-full p-1 border border-white/20 dark:border-gray-600/50">
              <button
                onClick={() => setTheme("light")}
                className={`p-2 rounded-full transition-colors ${theme === "light" ? "bg-white/40 dark:bg-gray-700/40 shadow-sm text-blue-500" : "hover:bg-white/50 dark:hover:bg-gray-700/50 text-gray-500"}`}
                title="روشن"
              >
                <Sun size={18} />
              </button>
              <button
                onClick={() => setTheme("system")}
                className={`p-2 rounded-full transition-colors ${theme === "system" ? "bg-white/40 dark:bg-gray-700/40 shadow-sm text-blue-500" : "hover:bg-white/50 dark:hover:bg-gray-700/50 text-gray-500"}`}
                title="سیستم"
              >
                <Monitor size={18} />
              </button>
              <button
                onClick={() => setTheme("dark")}
                className={`p-2 rounded-full transition-colors ${theme === "dark" ? "bg-white/40 dark:bg-gray-700/40 shadow-sm text-blue-500" : "hover:bg-white/50 dark:hover:bg-gray-700/50 text-gray-500"}`}
                title="تاریک"
              >
                <Moon size={18} />
              </button>
            </div>
            
            {/* Mobile (Very small screens) Theme Toggle */}
            <div className="flex ls:hidden">
              <button
                onClick={() => setTheme(theme === 'light' ? 'dark' : theme === 'dark' ? 'system' : 'light')}
                className="p-2 rounded-full bg-white/20 dark:bg-gray-700/40 border border-white/20 dark:border-gray-600/50 text-blue-600 dark:text-blue-400"
              >
                {theme === 'light' ? <Sun size={18} /> : theme === 'dark' ? <Moon size={18} /> : <Monitor size={18} />}
              </button>
            </div>
          </div>
        </GlassCard>
      </header>

      {/* Main Content */}
      <main className="relative z-10 flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-28 md:pb-8">
        <Outlet />
      </main>

      <footer className="relative z-10 mt-auto border-t border-white/20 dark:border-white/10 bg-white/30 dark:bg-gray-900/30 backdrop-blur-md pb-24 md:pb-0">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row justify-between items-center gap-4">
          <p className="text-sm text-gray-600 dark:text-gray-400">
            © 1405 سامانه گلزار تربت. تمامی حقوق محفوظ است.
          </p>
          <Link to="/admin" className="text-sm text-gray-500 hover:text-blue-500 transition-colors flex items-center gap-1.5">
            <Settings size={14} />
            ورود ادمین
          </Link>
        </div>
      </footer>

      {/* Mobile Bottom Navigation */}
      <nav className="md:hidden fixed bottom-3 left-3 right-3 z-50">
        <GlassCard className="flex items-center justify-between p-2 rounded-full shadow-[0_10px_40px_-10px_rgba(0,0,0,0.3)]">
          {navLinks.map((link) => {
            const Icon = link.icon;
            const isActive = location.pathname === link.path;
            return (
              <Link
                key={link.path}
                to={link.path}
                className={`relative flex flex-col items-center justify-center w-20 h-14 rounded-full pl-2 pr-2 transition-colors ${
                  isActive
                    ? "text-blue-600 dark:text-blue-400"
                    : "text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                }`}
              >
                {isActive && (
                  <motion.div
                    layoutId="mobileActiveTab"
                    className="absolute inset-0 bg-blue-50/50 dark:bg-blue-900/30 rounded-full"
                    transition={{
                      type: "spring",
                      stiffness: 400,
                      damping: 30,
                    }}
                  />
                )}
                <span className="relative z-10 flex flex-col items-center">
                  <Icon
                    size={22}
                    className={
                      isActive ? "mb-1" : "mb-1 opacity-80"
                    }
                  />
                  <span className="text-[10px]">
                    {link.name}
                  </span>
                </span>
              </Link>
            );
          })}
        </GlassCard>
      </nav>
    </div>
  );
}