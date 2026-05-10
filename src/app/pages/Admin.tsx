import { useState, useEffect } from "react";
import { GlassCard } from "../components/GlassCard";
import { getMartyrs, deleteMartyr, updateMartyr, getSubmissions, deleteSubmission, approveSubmission, Martyr, getSliders, addSlider, deleteSlider, reorderSliders, Slider } from "../data/db";
import { Lock, LogOut, Search, Trash2, Edit, Upload, CheckCircle, XCircle, Image as ImageIcon, Users, FileText, Plus, ChevronUp, ChevronDown, Save, X } from "lucide-react";
import { motion, AnimatePresence } from "motion/react";
import { ImageWithRoseFallback } from "../components/ImageWithRoseFallback";

export function Admin() {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");

  const [activeTab, setActiveTab] = useState<"martyrs" | "gallery" | "submissions">("martyrs");
  const [searchTerm, setSearchTerm] = useState("");
  const [martyrs, setMartyrs] = useState<Martyr[]>([]);
  const [submissions, setSubmissions] = useState(getSubmissions());
  const [sliders, setSliders] = useState<Slider[]>([]);

  // Editing martyr state
  const [editingMartyr, setEditingMartyr] = useState<Martyr | null>(null);

  useEffect(() => {
    document.title = "گلزار تربت - پنل مدیریت";
    const auth = sessionStorage.getItem("admin_auth");
    if (auth === "true") setIsAuthenticated(true);
    setMartyrs(getMartyrs());
    setSliders(getSliders());
  }, []);

  const handleLogin = (e: React.FormEvent) => {
    e.preventDefault();
    if (username === "admin" && password === "admin123") {
      setIsAuthenticated(true);
      sessionStorage.setItem("admin_auth", "true");
      setError("");
    } else {
      setError("نام کاربری یا رمز عبور اشتباه است");
    }
  };

  const handleLogout = () => {
    setIsAuthenticated(false);
    sessionStorage.removeItem("admin_auth");
    setPassword("");
    setUsername("");
  };

  const handleFileUpload = (e: React.ChangeEvent<HTMLInputElement>, martyr: Martyr) => {
    const file = e.target.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        const result = reader.result as string;
        const updated = { ...martyr, profileImage: result };
        updateMartyr(updated);
        setMartyrs(getMartyrs());
        alert(`تصویر با نام ${martyr.id} ذخیره شد`);
      };
      reader.readAsDataURL(file);
    }
  };

  const handleDeleteMartyr = (id: string) => {
    if (window.confirm("آیا از حذف این شهید اطمینان دارید؟")) {
      deleteMartyr(id);
      setMartyrs(getMartyrs());
    }
  };

  const handleSaveMartyr = (e: React.FormEvent) => {
    e.preventDefault();
    if (editingMartyr) {
      updateMartyr(editingMartyr);
      setMartyrs(getMartyrs());
      setEditingMartyr(null);
      alert("تغییرات با موفقیت ذخیره شد");
    }
  };

  const handleDeleteSubmission = (id: string) => {
    if (window.confirm("آیا از حذف این اثر از دیتابیس و فایل‌ها اطمینان دارید؟")) {
      deleteSubmission(id);
      setSubmissions(getSubmissions());
    }
  };

  const handleSliderUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        const result = reader.result as string;
        addSlider(result);
        setSliders(getSliders());
      };
      reader.readAsDataURL(file);
    }
  };

  const handleDeleteSlider = (id: string) => {
    if (window.confirm("آیا از حذف این تصویر اسلایدر اطمینان دارید؟")) {
      deleteSlider(id);
      setSliders(getSliders());
    }
  };

  const moveSlider = (index: number, direction: 'up' | 'down') => {
    const newSliders = [...sliders];
    if (direction === 'up' && index > 0) {
      [newSliders[index - 1], newSliders[index]] = [newSliders[index], newSliders[index - 1]];
    } else if (direction === 'down' && index < newSliders.length - 1) {
      [newSliders[index + 1], newSliders[index]] = [newSliders[index], newSliders[index + 1]];
    }
    reorderSliders(newSliders);
    setSliders(getSliders());
  };

  const filteredMartyrs = martyrs.filter(m => {
    const term = searchTerm.toLowerCase();
    return (
      `${m.firstName} ${m.lastName}`.includes(term) ||
      m.nationalId.includes(term) ||
      m.codeIsar.includes(term)
    );
  });

  if (!isAuthenticated) {
    return (
      <div className="flex items-center justify-center min-h-[60vh]">
        <GlassCard className="w-full max-w-md p-8">
          <div className="text-center mb-8">
            <div className="inline-flex p-4 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-full mb-4">
              <Lock size={32} />
            </div>
            <h1 className="text-2xl font-bold text-gray-800 dark:text-white">ورود به پنل مدیریت</h1>
            <p className="text-gray-500 dark:text-gray-400 mt-2">اطلاعات ورود را وارد کنید</p>
          </div>
          <form onSubmit={handleLogin} className="space-y-4">
            <div>
              <input
                type="text"
                value={username}
                onChange={(e) => setUsername(e.target.value)}
                placeholder="نام کاربری..."
                className="w-full p-4 border border-white/20 bg-white/50 dark:bg-black/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 backdrop-blur-md"
                dir="ltr"
              />
            </div>
            <div>
              <input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="رمز عبور..."
                className="w-full p-4 border border-white/20 bg-white/50 dark:bg-black/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 backdrop-blur-md"
                dir="ltr"
              />
            </div>
            {error && <p className="text-rose-500 text-sm">{error}</p>}
            <button
              type="submit"
              className="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-colors"
            >
              ورود
            </button>
          </form>
        </GlassCard>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
        <h1 className="text-3xl font-bold text-gray-800 dark:text-white">پنل مدیریت</h1>
        <button
          onClick={handleLogout}
          className="flex items-center gap-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 px-4 py-2 rounded-xl transition-colors font-medium"
        >
          <LogOut size={18} />
          خروج
        </button>
      </div>

      <GlassCard className="p-2 !rounded-2xl">
        <div className="flex overflow-x-auto hide-scrollbar gap-2">
          <button
            onClick={() => setActiveTab("martyrs")}
            className={`flex items-center gap-2 px-6 py-3 rounded-xl font-medium transition-all whitespace-nowrap ${
              activeTab === "martyrs" 
                ? "bg-blue-500 text-white shadow-md" 
                : "text-gray-600 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-white/10"
            }`}
          >
            <Users size={18} />
            مدیریت شهدا
          </button>
          <button
            onClick={() => setActiveTab("submissions")}
            className={`flex items-center gap-2 px-6 py-3 rounded-xl font-medium transition-all whitespace-nowrap ${
              activeTab === "submissions" 
                ? "bg-blue-500 text-white shadow-md" 
                : "text-gray-600 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-white/10"
            }`}
          >
            <FileText size={18} />
            بررسی آثار ارسالی
          </button>
          <button
            onClick={() => setActiveTab("gallery")}
            className={`flex items-center gap-2 px-6 py-3 rounded-xl font-medium transition-all whitespace-nowrap ${
              activeTab === "gallery" 
                ? "bg-blue-500 text-white shadow-md" 
                : "text-gray-600 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-white/10"
            }`}
          >
            <ImageIcon size={18} />
            اسلایدر و گالری
          </button>
        </div>
      </GlassCard>

      {activeTab === "martyrs" && (
        <div className="space-y-6">
          <GlassCard className="p-4">
            <div className="relative">
              <Search className="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400" />
              <input
                type="text"
                placeholder="جستجو با نام، کد ملی یا کد ایثار..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full pr-12 pl-4 py-3 border border-white/20 bg-white/30 dark:bg-black/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </GlassCard>

          <div className="bg-white/40 dark:bg-black/20 rounded-2xl border border-white/20 overflow-hidden shadow-sm backdrop-blur-md">
            <div className="overflow-x-auto">
              <table className="w-full text-right text-sm">
                <thead className="bg-white/50 dark:bg-white/5 text-gray-700 dark:text-gray-200">
                  <tr>
                    <th className="p-4 font-bold">تصویر</th>
                    <th className="p-4 font-bold">نام و نام خانوادگی</th>
                    <th className="p-4 font-bold">کد ملی</th>
                    <th className="p-4 font-bold">کد ایثار</th>
                    <th className="p-4 font-bold">عملیات</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200/30 dark:divide-gray-700/30">
                  {filteredMartyrs.slice(0, 50).map((martyr) => (
                    <tr key={martyr.id} className="hover:bg-white/20 dark:hover:bg-white/5 transition-colors">
                      <td className="p-4">
                        <div className="w-12 h-12 rounded-lg overflow-hidden border border-white/20">
                          <ImageWithRoseFallback src={martyr.profileImage} alt={martyr.firstName} className="w-full h-full object-cover" />
                        </div>
                      </td>
                      <td className="p-4 font-medium text-gray-800 dark:text-gray-200">
                        {martyr.firstName} {martyr.lastName}
                      </td>
                      <td className="p-4 font-mono text-gray-600 dark:text-gray-400">{martyr.nationalId}</td>
                      <td className="p-4 font-mono text-gray-600 dark:text-gray-400">{martyr.codeIsar}</td>
                      <td className="p-4">
                        <div className="flex items-center gap-2">
                          <button
                            onClick={() => setEditingMartyr(martyr)}
                            className="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 rounded-lg transition-colors"
                            title="ویرایش مشخصات"
                          >
                            <Edit size={16} />
                          </button>
                          <label className="cursor-pointer p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 rounded-lg transition-colors" title="آپلود تصویر (نام گذاری با ID)">
                            <Upload size={16} />
                            <input type="file" accept="image/*" className="hidden" onChange={(e) => handleFileUpload(e, martyr)} />
                          </label>
                          <button
                            onClick={() => handleDeleteMartyr(martyr.id)}
                            className="p-2 bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 rounded-lg transition-colors"
                            title="حذف"
                          >
                            <Trash2 size={16} />
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
              {filteredMartyrs.length > 50 && (
                <div className="p-4 text-center text-sm text-gray-500">
                  فقط ۵۰ نتیجه اول نمایش داده می‌شود. برای یافتن موارد دیگر جستجو کنید.
                </div>
              )}
            </div>
          </div>
        </div>
      )}

      {/* Editing Modal */}
      <AnimatePresence>
        {editingMartyr && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm overflow-y-auto"
          >
            <motion.div
              initial={{ scale: 0.95, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.95, opacity: 0 }}
              className="bg-white dark:bg-gray-900 w-full max-w-3xl rounded-3xl shadow-2xl overflow-hidden my-auto mt-20 md:mt-auto"
            >
              <div className="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-800">
                <h3 className="text-xl font-bold text-gray-900 dark:text-white">ویرایش مشخصات شهید</h3>
                <button onClick={() => setEditingMartyr(null)} className="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                  <X size={24} />
                </button>
              </div>
              <div className="p-6 overflow-y-auto max-h-[60vh] hide-scrollbar">
                <form id="edit-martyr-form" onSubmit={handleSaveMartyr} className="space-y-6">
                  {/* Basic Information */}
                  <div className="bg-blue-50/50 dark:bg-blue-900/20 p-4 rounded-xl">
                    <h4 className="text-sm font-bold text-blue-700 dark:text-blue-300 mb-3">اطلاعات اصلی</h4>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">نام</label>
                        <input type="text" value={editingMartyr.firstName} onChange={(e) => setEditingMartyr({...editingMartyr, firstName: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">نام خانوادگی</label>
                        <input type="text" value={editingMartyr.lastName} onChange={(e) => setEditingMartyr({...editingMartyr, lastName: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">نام پدر</label>
                        <input type="text" value={editingMartyr.fatherName} onChange={(e) => setEditingMartyr({...editingMartyr, fatherName: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">کد ملی</label>
                        <input type="text" value={editingMartyr.nationalId} onChange={(e) => setEditingMartyr({...editingMartyr, nationalId: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">کد ایثار</label>
                        <input type="text" value={editingMartyr.codeIsar} onChange={(e) => setEditingMartyr({...editingMartyr, codeIsar: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">وضعیت ایثارگری</label>
                        <input type="text" value={editingMartyr.veteranStatus} onChange={(e) => setEditingMartyr({...editingMartyr, veteranStatus: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                    </div>
                  </div>

                  {/* Personal Information */}
                  <div className="bg-emerald-50/50 dark:bg-emerald-900/20 p-4 rounded-xl">
                    <h4 className="text-sm font-bold text-emerald-700 dark:text-emerald-300 mb-3">مشخصات فردی</h4>
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">جنسیت</label>
                        <input type="text" value={editingMartyr.gender} onChange={(e) => setEditingMartyr({...editingMartyr, gender: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ملیت</label>
                        <input type="text" value={editingMartyr.nationality} onChange={(e) => setEditingMartyr({...editingMartyr, nationality: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">مذهب</label>
                        <input type="text" value={editingMartyr.religion} onChange={(e) => setEditingMartyr({...editingMartyr, religion: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">وضعیت تاهل</label>
                        <input type="text" value={editingMartyr.maritalStatus} onChange={(e) => setEditingMartyr({...editingMartyr, maritalStatus: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">تحصیلات</label>
                        <input type="text" value={editingMartyr.education} onChange={(e) => setEditingMartyr({...editingMartyr, education: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">شغل</label>
                        <input type="text" value={editingMartyr.occupation} onChange={(e) => setEditingMartyr({...editingMartyr, occupation: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">سن (در زمان شهادت)</label>
                        <input type="number" value={editingMartyr.age ?? ''} onChange={(e) => setEditingMartyr({...editingMartyr, age: e.target.value ? parseInt(e.target.value) : null})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                    </div>
                  </div>

                  {/* Dates */}
                  <div className="bg-amber-50/50 dark:bg-amber-900/20 p-4 rounded-xl">
                    <h4 className="text-sm font-bold text-amber-700 dark:text-amber-300 mb-3">تاریخ‌ها</h4>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">تاریخ تولد</label>
                        <div className="grid grid-cols-3 gap-2">
                          <input type="text" placeholder="روز" value={editingMartyr.birthDay} onChange={(e) => setEditingMartyr({...editingMartyr, birthDay: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-center" />
                          <input type="text" placeholder="ماه" value={editingMartyr.birthMonth} onChange={(e) => setEditingMartyr({...editingMartyr, birthMonth: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-center" />
                          <input type="text" placeholder="سال" value={editingMartyr.birthYear} onChange={(e) => setEditingMartyr({...editingMartyr, birthYear: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-center" />
                        </div>
                        <input type="text" placeholder="تاریخ کامل (اختیاری)" value={editingMartyr.birthDate} onChange={(e) => setEditingMartyr({...editingMartyr, birthDate: e.target.value})} className="w-full mt-2 p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">تاریخ شهادت</label>
                        <div className="grid grid-cols-3 gap-2">
                          <input type="text" placeholder="روز" value={editingMartyr.martyrdomDay} onChange={(e) => setEditingMartyr({...editingMartyr, martyrdomDay: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-center" />
                          <input type="text" placeholder="ماه" value={editingMartyr.martyrdomMonth} onChange={(e) => setEditingMartyr({...editingMartyr, martyrdomMonth: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-center" />
                          <input type="text" placeholder="سال" value={editingMartyr.martyrdomYear} onChange={(e) => setEditingMartyr({...editingMartyr, martyrdomYear: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-center" />
                        </div>
                        <input type="text" placeholder="تاریخ کامل (اختیاری)" value={editingMartyr.martyrdomDate} onChange={(e) => setEditingMartyr({...editingMartyr, martyrdomDate: e.target.value})} className="w-full mt-2 p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                    </div>
                  </div>

                  {/* Locations */}
                  <div className="bg-purple-50/50 dark:bg-purple-900/20 p-4 rounded-xl">
                    <h4 className="text-sm font-bold text-purple-700 dark:text-purple-300 mb-3">مکان‌ها</h4>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">محل تولد</label>
                        <input type="text" value={editingMartyr.birthPlace} onChange={(e) => setEditingMartyr({...editingMartyr, birthPlace: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">محل گلزار (دفن)</label>
                        <input type="text" value={editingMartyr.burialPlace} onChange={(e) => setEditingMartyr({...editingMartyr, burialPlace: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">محل پرونده</label>
                        <input type="text" value={editingMartyr.fileLocation} onChange={(e) => setEditingMartyr({...editingMartyr, fileLocation: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                    </div>
                  </div>

                  {/* Military Information */}
                  <div className="bg-rose-50/50 dark:bg-rose-900/20 p-4 rounded-xl">
                    <h4 className="text-sm font-bold text-rose-700 dark:text-rose-300 mb-3">اطلاعات نظامی</h4>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">یگان خدمت</label>
                        <input type="text" value={editingMartyr.servingUnit} onChange={(e) => setEditingMartyr({...editingMartyr, servingUnit: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">نوع عضویت</label>
                        <input type="text" value={editingMartyr.membershipType} onChange={(e) => setEditingMartyr({...editingMartyr, membershipType: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">عملیات نظامی</label>
                        <input type="text" value={editingMartyr.militaryOperation} onChange={(e) => setEditingMartyr({...editingMartyr, militaryOperation: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">منطقه عملیات</label>
                        <input type="text" value={editingMartyr.operationZone} onChange={(e) => setEditingMartyr({...editingMartyr, operationZone: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">جریان رویداد</label>
                        <input type="text" value={editingMartyr.eventStream} onChange={(e) => setEditingMartyr({...editingMartyr, eventStream: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">دشمن</label>
                        <input type="text" value={editingMartyr.enemy} onChange={(e) => setEditingMartyr({...editingMartyr, enemy: e.target.value})} className="w-full p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" />
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              <div className="p-6 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50 flex justify-end gap-3">
                <button
                  type="button"
                  onClick={() => setEditingMartyr(null)}
                  className="px-6 py-2.5 rounded-xl font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors"
                >
                  انصراف
                </button>
                <button
                  type="submit"
                  form="edit-martyr-form"
                  className="px-6 py-2.5 rounded-xl font-medium bg-blue-600 text-white hover:bg-blue-700 transition-colors flex items-center gap-2"
                >
                  <Save size={18} />
                  ذخیره تغییرات
                </button>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>

      {activeTab === "submissions" && (
        <div className="space-y-6">
          {submissions.length === 0 ? (
            <GlassCard className="p-10 text-center">
              <FileText className="w-16 h-16 mx-auto text-gray-400 mb-4 opacity-50" />
              <h3 className="text-xl font-bold text-gray-700 dark:text-gray-300">هیچ اثر ارسالی یافت نشد</h3>
            </GlassCard>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {submissions.map((sub) => {
                const martyr = martyrs.find(m => m.id === sub.martyrId);
                return (
                  <GlassCard key={sub.id} className="flex flex-col p-4 space-y-4">
                    <div className="flex justify-between items-start">
                      <span className={`px-2 py-1 text-xs rounded-md font-bold ${
                        sub.status === 'pending' ? 'bg-amber-100 text-amber-700' :
                        sub.status === 'approved' ? 'bg-emerald-100 text-emerald-700' :
                        'bg-rose-100 text-rose-700'
                      }`}>
                        {sub.status === 'pending' ? 'در انتظار بررسی' : sub.status === 'approved' ? 'تایید شده' : 'رد شده'}
                      </span>
                      <span className="text-xs text-gray-500" dir="ltr">{new Date(sub.submittedAt).toLocaleDateString('fa-IR')}</span>
                    </div>
                    <div className="flex-1 bg-black/5 rounded-xl flex items-center justify-center min-h-[150px] relative overflow-hidden">
                      {sub.type === 'image' ? (
                        <img src={sub.fileUrl} alt="رسانه" className="absolute inset-0 w-full h-full object-cover" />
                      ) : (
                        <span className="text-sm font-medium opacity-60">فایل ضمیمه ({sub.type})</span>
                      )}
                    </div>
                    <div className="text-sm">
                      <span className="text-gray-500">مربوط به شهید: </span>
                      <strong className="text-gray-800 dark:text-gray-200">
                        {martyr ? `${martyr.firstName} ${martyr.lastName}` : "نامشخص"}
                      </strong>
                    </div>
                    <div className="flex gap-2 pt-2 border-t border-white/20">
                      <button
                        onClick={() => {
                          approveSubmission(sub.id);
                          setSubmissions(getSubmissions());
                        }}
                        className="flex-1 flex items-center justify-center gap-2 py-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-900/30 rounded-xl"
                      >
                        <CheckCircle size={16} />
                        تایید
                      </button>
                      <button
                        onClick={() => handleDeleteSubmission(sub.id)}
                        className="flex-1 flex items-center justify-center gap-2 py-2 bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/30 rounded-xl"
                      >
                        <Trash2 size={16} />
                        حذف نهایی
                      </button>
                    </div>
                  </GlassCard>
                );
              })}
            </div>
          )}
        </div>
      )}

      {activeTab === "gallery" && (
        <div className="space-y-6">
          <GlassCard className="p-6">
            <div className="flex justify-between items-center mb-6">
              <div>
                <h2 className="text-xl font-bold text-gray-800 dark:text-white">مدیریت اسلایدر صفحه اصلی</h2>
                <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">تصاویر را برای اسلایدر صفحه اصلی اضافه، حذف یا مرتب کنید.</p>
              </div>
              <label className="cursor-pointer flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold transition-colors shadow-lg shadow-blue-600/20">
                <Plus size={20} />
                افزودن تصویر جدید
                <input type="file" accept="image/*" className="hidden" onChange={handleSliderUpload} />
              </label>
            </div>

            <div className="space-y-4">
              {sliders.map((slider, index) => (
                <div key={slider.id} className="flex items-center gap-4 bg-white/50 dark:bg-black/20 p-4 rounded-2xl border border-white/20">
                  <div className="w-24 h-16 rounded-lg overflow-hidden flex-shrink-0 border border-white/30">
                    <img src={slider.url} className="w-full h-full object-cover" alt="اسلایدر" />
                  </div>
                  <div className="flex-1 font-mono text-sm text-gray-500 truncate" dir="ltr">
                    {slider.url.length > 50 ? '...' + slider.url.slice(-50) : slider.url}
                  </div>
                  <div className="flex items-center gap-2 border-r border-gray-200 dark:border-gray-700 pr-4">
                    <div className="flex flex-col gap-1">
                      <button 
                        onClick={() => moveSlider(index, 'up')}
                        disabled={index === 0}
                        className="p-1 text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded disabled:opacity-30 disabled:hover:bg-transparent"
                      >
                        <ChevronUp size={16} />
                      </button>
                      <button 
                        onClick={() => moveSlider(index, 'down')}
                        disabled={index === sliders.length - 1}
                        className="p-1 text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded disabled:opacity-30 disabled:hover:bg-transparent"
                      >
                        <ChevronDown size={16} />
                      </button>
                    </div>
                    <button
                      onClick={() => handleDeleteSlider(slider.id)}
                      className="p-2 ml-2 bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/30 rounded-xl transition-colors"
                      title="حذف"
                    >
                      <Trash2 size={18} />
                    </button>
                  </div>
                </div>
              ))}
              {sliders.length === 0 && (
                <div className="text-center p-8 text-gray-500 bg-black/5 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
                  هیچ تصویری در اسلایدر وجود ندارد
                </div>
              )}
            </div>
          </GlassCard>
        </div>
      )}
    </div>
  );
}
