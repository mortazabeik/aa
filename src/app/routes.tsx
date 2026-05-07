import { createBrowserRouter } from "react-router";
import { Layout } from "./components/Layout";
import { Home } from "./pages/Home";
import { Search } from "./pages/Search";
import { MartyrDetail } from "./pages/MartyrDetail";
import { Submit } from "./pages/Submit";
import { Admin } from "./pages/Admin";

export const router = createBrowserRouter([
  {
    path: "/",
    Component: Layout,
    children: [
      { index: true, Component: Home },
      { path: "search", Component: Search },
      { path: "martyr/:id", Component: MartyrDetail },
      { path: "submit", Component: Submit },
      { path: "admin", Component: Admin },
    ],
  },
]);
