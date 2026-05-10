import { useState, useMemo, useEffect, useCallback } from "react";
import { Link, useSearchParams } from "react-router";
import { GlassCard } from "../components/GlassCard";
import { getMartyrs, Martyr } from "../data/db";
import { Search as SearchIcon, Filter, X, ChevronLeft, ChevronRight, ChevronDown } from "lucide-react";
import { motion, AnimatePresence } from "motion/react";
import { ImageWithRoseFallback } from "../components/ImageWithRoseFallback";

  const GlassSelect = ({ value, onChange, options, placeholder }: any) => (
    <div className="relative group">
      <select
        value={value}
        onChange={(e) => onChange(e.target.value)}
        className="appearance-none block w-full py-2 px-3 pr-8 border border-white/20 dark:border-white/10 bg-white/20 dark:bg-black/20 text-gray-800 dark:text-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 backdrop-blur-md shadow-sm text-sm"
      >
        <option value="" className="text-gray-900 bg-white dark:bg-gray-800 dark:text-gray-200">{placeholder}</option>
        {options.map((opt: string) => (
          <option key={opt} value={opt} className="text-gray-900 bg-white dark:bg-gray-800 dark:text-gray-200">{opt}</option>
        ))}
      </select>
      <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
        <ChevronDown size={14} />
      </div>
    </div>
  );

  const GlassCombobox = ({ value, onChange, options, placeholder, id }: any) => (
    <div className="relative group">
      <input
        list={id}
        value={value}
        onChange={(e) => onChange(e.target.value)}
        placeholder={placeholder}
        className="block w-full py-2 px-3 pr-8 border border-white/20 dark:border-white/10 bg-white/20 dark:bg-black/20 text-gray-800 dark:text-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 backdrop-blur-md shadow-sm text-sm placeholder-gray-500/70"
      />
      <datalist id={id}>
        {options.map((opt: string) => (
          <option key={opt} value={opt} />
        ))}
      </datalist>
      <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
        <ChevronDown size={14} />
      </div>
    </div>
  );

  const GlassInput = ({ value, onChange, placeholder, type = "text" }: any) => (
    <input
      type={type}
      value={value}
      onChange={(e) => onChange(e.target.value)} 
      placeholder={placeholder}
      className="block w-full py-2 px-3 border border-white/20 dark:border-white/10 bg-white/20 dark:bg-black/20 text-gray-800 dark:text-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 backdrop-blur-md shadow-sm text-sm placeholder-gray-500/70"
    />
  );
export function Search() {
  const [searchParams, setSearchParams] = useSearchParams();
  
  // Initialize state from URL params
  const getParam = (key: string) => searchParams.get(key) || "";
  
  const [searchTerm, setSearchTerm] = useState(getParam("search"));
  const [nationalId, setNationalId] = useState(getParam("nationalId"));
  const [codeIsar, setCodeIsar] = useState(getParam("codeIsar"));
  const [veteranStatus, setVeteranStatus] = useState(getParam("veteranStatus"));
  const [age, setAge] = useState(getParam("age"));
  const [birthYear, setBirthYear] = useState(getParam("birthYear"));
  const [birthMonth, setBirthMonth] = useState(getParam("birthMonth"));
  const [birthDay, setBirthDay] = useState(getParam("birthDay"));
  const [martyrdomYear, setMartyrdomYear] = useState(getParam("martyrdomYear"));
  const [martyrdomMonth, setMartyrdomMonth] = useState(getParam("martyrdomMonth"));
  const [martyrdomDay, setMartyrdomDay] = useState(getParam("martyrdomDay"));
  const [birthPlace, setBirthPlace] = useState(getParam("birthPlace"));
  const [burialPlace, setBurialPlace] = useState(getParam("burialPlace"));
  const [fileLocation, setFileLocation] = useState(getParam("fileLocation"));
  const [gender, setGender] = useState(getParam("gender"));
  const [nationality, setNationality] = useState(getParam("nationality"));
  const [religion, setReligion] = useState(getParam("religion"));
  const [occupation, setOccupation] = useState(getParam("occupation"));
  const [servingUnit, setServingUnit] = useState(getParam("servingUnit"));

  const [isFiltersOpen, setIsFiltersOpen] = useState(() => {
    // Open filters if any filter is active from URL
    return !!(getParam("nationalId") || getParam("codeIsar") || getParam("veteranStatus") || 
              getParam("age") || getParam("birthYear") || getParam("birthMonth") || getParam("birthDay") ||
              getParam("martyrdomYear") || getParam("martyrdomMonth") || getParam("martyrdomDay") ||
              getParam("birthPlace") || getParam("burialPlace") || getParam("fileLocation") ||
              getParam("gender") || getParam("nationality") || getParam("religion") ||
              getParam("occupation") || getParam("servingUnit"));
  });
  
  const [currentPage, setCurrentPage] = useState(() => {
    const pageParam = searchParams.get("page");
    return pageParam ? Math.max(1, parseInt(pageParam)) : 1;
  });
  const itemsPerPage = 24;

  const martyrsData = useMemo(() => getMartyrs(), []);

  useEffect(() => {
    document.title = "گلزار تربت - جستجوی پیشرفته";
  }, []);

  // Sync state to URL
  const updateURL = useCallback(() => {
    const params = new URLSearchParams();
    
    if (searchTerm) params.set("search", searchTerm);
    if (nationalId) params.set("nationalId", nationalId);
    if (codeIsar) params.set("codeIsar", codeIsar);
    if (veteranStatus) params.set("veteranStatus", veteranStatus);
    if (age) params.set("age", age);
    if (birthYear) params.set("birthYear", birthYear);
    if (birthMonth) params.set("birthMonth", birthMonth);
    if (birthDay) params.set("birthDay", birthDay);
    if (martyrdomYear) params.set("martyrdomYear", martyrdomYear);
    if (martyrdomMonth) params.set("martyrdomMonth", martyrdomMonth);
    if (martyrdomDay) params.set("martyrdomDay", martyrdomDay);
    if (birthPlace) params.set("birthPlace", birthPlace);
    if (burialPlace) params.set("burialPlace", burialPlace);
    if (fileLocation) params.set("fileLocation", fileLocation);
    if (gender) params.set("gender", gender);
    if (nationality) params.set("nationality", nationality);
    if (religion) params.set("religion", religion);
    if (occupation) params.set("occupation", occupation);
    if (servingUnit) params.set("servingUnit", servingUnit);
    if (currentPage > 1) params.set("page", currentPage.toString());
    
    setSearchParams(params, { replace: true });
  }, [searchTerm, nationalId, codeIsar, veteranStatus, age, birthYear, birthMonth, birthDay,
      martyrdomYear, martyrdomMonth, martyrdomDay, birthPlace, burialPlace, fileLocation,
      gender, nationality, religion, occupation, servingUnit, currentPage, setSearchParams]);

  useEffect(() => {
    updateURL();
  }, [updateURL]);
  
  const getUnique = (key: keyof Martyr) => 
    Array.from(new Set(martyrsData.map(m => m[key] as string).filter(Boolean)));

  const uniqueVeteranStatus = getUnique("veteranStatus");
  const uniqueBirthPlaces = getUnique("birthPlace");
  const uniqueBurialPlaces = getUnique("burialPlace");
  const uniqueFileLocations = getUnique("fileLocation");
  const uniqueGenders = getUnique("gender");
  const uniqueNationalities = getUnique("nationality");
  const uniqueReligions = getUnique("religion");
  const uniqueOccupations = getUnique("occupation");
  const uniqueServingUnits = getUnique("servingUnit");

  const filteredMartyrs = useMemo(() => {
    return martyrsData.filter(m => {
      const fullName = `${m.firstName} ${m.lastName}`;
      const matchName = fullName.includes(searchTerm) || (m.firstName && m.firstName.includes(searchTerm)) || (m.lastName && m.lastName.includes(searchTerm));
      const matchNId = nationalId === "" || m.nationalId.includes(nationalId);
      const matchCodeIsar = codeIsar === "" || m.codeIsar.includes(codeIsar);
      const matchVet = veteranStatus === "" || (m.veteranStatus && m.veteranStatus.includes(veteranStatus));
      const matchAge = age === "" || m.age?.toString() === age;
      const matchBYear = birthYear === "" || m.birthYear === birthYear;
      const matchBMonth = birthMonth === "" || parseInt(m.birthMonth || "0") === parseInt(birthMonth);
      const matchBDay = birthDay === "" || parseInt(m.birthDay || "0") === parseInt(birthDay);
      const matchMYear = martyrdomYear === "" || m.martyrdomYear === martyrdomYear;
      const matchMMonth = martyrdomMonth === "" || parseInt(m.martyrdomMonth || "0") === parseInt(martyrdomMonth);
      const matchMDay = martyrdomDay === "" || parseInt(m.martyrdomDay || "0") === parseInt(martyrdomDay);
      const matchBP = birthPlace === "" || (m.birthPlace && m.birthPlace.includes(birthPlace));
      const matchBurial = burialPlace === "" || (m.burialPlace && m.burialPlace.includes(burialPlace));
      const matchFileLoc = fileLocation === "" || (m.fileLocation && m.fileLocation.includes(fileLocation));
      const matchGender = gender === "" || m.gender === gender;
      const matchNat = nationality === "" || m.nationality === nationality;
      const matchRel = religion === "" || m.religion === religion;
      const matchOcc = occupation === "" || (m.occupation && m.occupation.includes(occupation));
      const matchSU = servingUnit === "" || (m.servingUnit && m.servingUnit.includes(servingUnit));

      return matchName && matchNId && matchCodeIsar && matchVet && matchAge &&
             matchBYear && matchBMonth && matchBDay &&
             matchMYear && matchMMonth && matchMDay &&
             matchBP && matchBurial && matchFileLoc &&
             matchGender && matchNat && matchRel && matchOcc && matchSU;
    });
  }, [
    martyrsData, searchTerm, nationalId, codeIsar, veteranStatus, age,
    birthYear, birthMonth, birthDay, martyrdomYear, martyrdomMonth, martyrdomDay,
    birthPlace, burialPlace, fileLocation, gender, nationality, religion, occupation, servingUnit
  ]);

  const totalPages = Math.ceil(filteredMartyrs.length / itemsPerPage);
  const paginatedMartyrs = filteredMartyrs.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

  const clearFilters = () => {
    setSearchTerm(""); setNationalId(""); setCodeIsar(""); setVeteranStatus(""); setAge("");
    setBirthYear(""); setBirthMonth(""); setBirthDay("");
    setMartyrdomYear(""); setMartyrdomMonth(""); setMartyrdomDay("");
    setBirthPlace(""); setBurialPlace(""); setFileLocation(""); setGender("");
    setNationality(""); setReligion(""); setOccupation(""); setServingUnit("");
    setCurrentPage(1);
  };

  const handleFilterChange = (setter: React.Dispatch<React.SetStateAction<string>>) => (val: string) => {
    setter(val);
    setCurrentPage(1);
  };



  const isAnyFilterActive = nationalId || codeIsar || veteranStatus || age ||
    birthYear || birthMonth || birthDay || martyrdomYear || martyrdomMonth || martyrdomDay ||
    birthPlace || burialPlace || fileLocation || gender || nationality || religion || occupation || servingUnit;

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold text-gray-800 dark:text-white">جستجوی شهدا</h1>
      </div>

      <GlassCard className="p-4 md:p-6 !border-white/30">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="relative flex-1">
            <div className="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
              <SearchIcon className="h-5 w-5 text-gray-400" />
            </div>
            <input
              type="text"
              placeholder="جستجو با نام شهید..."
              value={searchTerm}
              onChange={(e) => handleFilterChange(setSearchTerm)(e.target.value)}
              className="block w-full pr-12 pl-4 py-3.5 border border-white/20 dark:border-white/10 rounded-2xl bg-white/30 dark:bg-black/20 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 backdrop-blur-lg shadow-inner"
            />
          </div>
          <button
            onClick={() => setIsFiltersOpen(!isFiltersOpen)}
            className={`flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl font-medium transition-all duration-300 shadow-sm border backdrop-blur-md ${
              isFiltersOpen || isAnyFilterActive
                ? "bg-blue-500/20 border-blue-500/30 text-blue-700 dark:text-blue-300"
                : "bg-white/20 dark:bg-white/5 border-white/20 dark:border-white/10 text-gray-700 dark:text-gray-300"
            }`}
          >
            <Filter size={18} />
            فیلتر پیشرفته
          </button>
        </div>

        <AnimatePresence>
          {isFiltersOpen && (
            <motion.div
              initial={{ height: 0, opacity: 0 }}
              animate={{ height: "auto", opacity: 1 }}
              exit={{ height: 0, opacity: 0 }}
              className="overflow-hidden"
            >
              <div className="pt-6 pb-2 border-t border-white/20 dark:border-white/10 mt-6">
                <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                  <div className="space-y-1">
                    <label className="text-xs font-bold text-gray-600 dark:text-gray-300 pr-1">کد ملی</label>
                    <GlassInput value={nationalId} onChange={handleFilterChange(setNationalId)} placeholder="مثال: ...070" />
                  </div>
                  <div className="space-y-1">
                    <label className="text-xs font-bold text-gray-600 dark:text-gray-300 pr-1">کد ایثار</label>
                    <GlassInput value={codeIsar} onChange={handleFilterChange(setCodeIsar)} placeholder="کد ایثار..." />
                  </div>
                  <div className="space-y-1">
                    <label className="text-xs font-bold text-gray-600 dark:text-gray-300 pr-1">ایثارگری</label>
                    <GlassCombobox id="list-vet" value={veteranStatus} onChange={handleFilterChange(setVeteranStatus)} options={uniqueVeteranStatus} placeholder="جستجو و انتخاب..." />
                  </div>
                  <div className="space-y-1">
                    <label className="text-xs font-bold text-gray-600 dark:text-gray-300 pr-1">سن شهادت</label>
                    <GlassInput type="number" value={age} onChange={handleFilterChange(setAge)} placeholder="مثال: ۲۰" />
                  </div>
                  <div className="space-y-1">
                    <label className="text-xs font-bold text-gray-600 dark:text-gray-300 pr-1">جنسیت</label>
                    <GlassSelect value={gender} onChange={handleFilterChange(setGender)} options={uniqueGenders} placeholder="همه" />
                  </div>
                  <div className="space-y-1">
                    <label className="text-xs font-bold text-gray-600 dark:text-gray-300 pr-1">ملیت</label>
                    <GlassSelect value={nationality} onChange={handleFilterChange(setNationality)} options={uniqueNationalities} placeholder="همه" />
                  </div>

                  {/* Dates */}
                  <div className="col-span-2 md:col-span-3 space-y-1 bg-white/10 dark:bg-black/10 p-2 rounded-xl border border-white/10">
                    <label className="text-xs font-bold text-gray-600 dark:text-gray-300 pr-1 block mb-2">تاریخ تولد</label>
                    <div className="flex gap-2">
                      <GlassInput type="number" value={birthDay} onChange={handleFilterChange(setBirthDay)} placeholder="روز (۱-۳۱)" />
                      <GlassInput type="number" value={birthMonth} onChange={handleFilterChange(setBirthMonth)} placeholder="ماه (۱-۱۲)" />
                      <GlassInput type="number" value={birthYear} onChange={handleFilterChange(setBirthYear)} placeholder="سال (..۱۳)" />
                    </div>
                  </div>
                  <div className="col-span-2 md:col-span-3 space-y-1 bg-white/10 dark:bg-black/10 p-2 rounded-xl border border-white/10">
                    <label className="text-xs font-bold text-gray-600 dark:text-gray-300 pr-1 block mb-2">تاریخ شهادت</label>
                    <div className="flex gap-2">
                      <GlassInput type="number" value={martyrdomDay} onChange={handleFilterChange(setMartyrdomDay)} placeholder="روز (۱-۳۱)" />
                      <GlassInput type="number" value={martyrdomMonth} onChange={handleFilterChange(setMartyrdomMonth)} placeholder="ماه (۱-۱۲)" />
                      <GlassInput type="number" value={martyrdomYear} onChange={handleFilterChange(setMartyrdomYear)} placeholder="سال (..۱۳)" />
                    </div>
                  </div>

                  <div className="space-y-1 col-span-2 md:col-span-2">
                    <label className="text-xs font-bold text-gray-600 dark:text-gray-300 pr-1">محل تولد</label>
                    <GlassCombobox id="list-bp" value={birthPlace} onChange={handleFilterChange(setBirthPlace)} options={uniqueBirthPlaces} placeholder="جستجوی منطقه..." />
                  </div>
                  <div className="space-y-1 col-span-2 md:col-span-2">
                    <label className="text-xs font-bold text-gray-600 dark:text-gray-300 pr-1">محل گلزار</label>
                    <GlassCombobox id="list-bur" value={burialPlace} onChange={handleFilterChange(setBurialPlace)} options={uniqueBurialPlaces} placeholder="جستجوی گلزار..." />
                  </div>
                  <div className="space-y-1 col-span-2 md:col-span-2">
                    <label className="text-xs font-bold text-gray-600 dark:text-gray-300 pr-1">محل پرونده</label>
                    <GlassCombobox id="list-file" value={fileLocation} onChange={handleFilterChange(setFileLocation)} options={uniqueFileLocations} placeholder="جستجوی محل پرونده..." />
                  </div>

                  <div className="space-y-1 col-span-2 md:col-span-2">
                    <label className="text-xs font-bold text-gray-600 dark:text-gray-300 pr-1">مذهب</label>
                    <GlassSelect value={religion} onChange={handleFilterChange(setReligion)} options={uniqueReligions} placeholder="همه" />
                  </div>
                  <div className="space-y-1 col-span-2 md:col-span-2">
                    <label className="text-xs font-bold text-gray-600 dark:text-gray-300 pr-1">شغل</label>
                    <GlassCombobox id="list-occ" value={occupation} onChange={handleFilterChange(setOccupation)} options={uniqueOccupations} placeholder="جستجوی شغل..." />
                  </div>
                  <div className="space-y-1 col-span-2 md:col-span-2">
                    <label className="text-xs font-bold text-gray-600 dark:text-gray-300 pr-1">یگان خدمت</label>
                    <GlassCombobox id="list-su" value={servingUnit} onChange={handleFilterChange(setServingUnit)} options={uniqueServingUnits} placeholder="جستجوی یگان..." />
                  </div>
                </div>

                <div className="flex justify-end mt-4">
                  <button
                    onClick={clearFilters}
                    className="text-sm font-medium text-rose-500 hover:text-rose-600 flex items-center gap-1.5 px-4 py-2 rounded-lg bg-rose-50/50 dark:bg-rose-900/20 transition-colors border border-rose-100/50 dark:border-rose-800/30"
                  >
                    <X size={14} />
                    پاک کردن همه فیلترها
                  </button>
                </div>
              </div>
            </motion.div>
          )}
        </AnimatePresence>
      </GlassCard>

      {/* Results */}
      <div className="space-y-6">
        <p className="text-sm font-medium text-gray-600 dark:text-gray-400 px-2">
          {filteredMartyrs.length} نتیجه یافت شد
        </p>

        {paginatedMartyrs.length > 0 ? (
          <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {paginatedMartyrs.map((martyr) => (
                <GlassCard key={martyr.id} hoverEffect className="overflow-hidden flex flex-col p-0 !rounded-2xl">
                  <div className="aspect-[3/4] overflow-hidden relative">
                    <ImageWithRoseFallback 
                      src={martyr.profileImage} 
                      alt={`${martyr.firstName} ${martyr.lastName}`} 
                      className="w-full h-full object-cover transition-transform duration-700 hover:scale-110"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-5">
                      <h3 className="text-white font-bold text-xl leading-tight mb-1.5">{martyr.firstName} {martyr.lastName}</h3>
                      <p className="text-gray-300 text-xs font-medium bg-white/20 backdrop-blur-md w-fit px-2 py-0.5 rounded-md">{martyr.birthPlace || "نامشخص"}</p>
                    </div>
                  </div>
                  <div className="p-5 flex-1 flex flex-col justify-between gap-5 bg-white/10 dark:bg-black/10">
                    <div className="text-sm text-gray-700 dark:text-gray-300 space-y-2 font-mono">
                      <div className="flex justify-between border-b border-gray-200/50 dark:border-gray-700/30 pb-2">
                        <span className="text-gray-500 dark:text-gray-400">ولادت:</span>
                        <span className="font-bold">{martyr.birthDate || "-"}</span>
                      </div>
                      <div className="flex justify-between pt-1">
                        <span className="text-gray-500 dark:text-gray-400">شهادت:</span>
                        <span className="text-rose-500 dark:text-rose-400 font-bold">{martyr.martyrdomDate || "-"}</span>
                      </div>
                    </div>
                    <Link 
                      to={`/martyr/${martyr.id}`}
                      className="w-full py-2.5 text-center text-sm font-bold bg-white/40 hover:bg-white/60 dark:bg-white/10 dark:hover:bg-white/20 text-gray-800 dark:text-gray-100 rounded-xl transition-all shadow-sm border border-white/30 dark:border-white/5"
                    >
                      مشاهده پروفایل
                    </Link>
                  </div>
                </GlassCard>
              ))}
            </div>

            {totalPages > 1 && (
              <div className="flex justify-center items-center gap-2 pt-8" dir="rtl">
                <button
                  onClick={() => setCurrentPage(p => Math.max(1, p - 1))}
                  disabled={currentPage === 1}
                  className="p-2 rounded-xl bg-white/40 dark:bg-gray-800/40 border border-white/20 dark:border-gray-700/30 disabled:opacity-50 hover:bg-white/60 transition-colors"
                >
                  <ChevronRight size={20} className="text-gray-700 dark:text-gray-300" />
                </button>
                
                <div className="flex gap-1 flex-wrap justify-center max-w-[200px] overflow-hidden">
                  {Array.from({ length: totalPages }, (_, i) => i + 1)
                    .filter(p => p === 1 || p === totalPages || Math.abs(p - currentPage) <= 1)
                    .map((page, index, array) => {
                      if (index > 0 && page - array[index - 1] > 1) {
                        return <span key={`ellipsis-${page}`} className="px-2">...</span>;
                      }
                      return (
                        <button
                          key={page}
                          onClick={() => setCurrentPage(page)}
                          className={`w-10 h-10 rounded-xl font-medium transition-all ${
                            currentPage === page
                              ? "bg-blue-600 text-white shadow-md shadow-blue-500/20 border border-blue-500"
                              : "bg-white/40 dark:bg-gray-800/40 text-gray-700 dark:text-gray-300 border border-white/20 dark:border-gray-700/30 hover:bg-white/60"
                          }`}
                        >
                          {page}
                        </button>
                      )
                    })}
                </div>

                <button
                  onClick={() => setCurrentPage(p => Math.min(totalPages, p + 1))}
                  disabled={currentPage === totalPages}
                  className="p-2 rounded-xl bg-white/40 dark:bg-gray-800/40 border border-white/20 dark:border-gray-700/30 disabled:opacity-50 hover:bg-white/60 transition-colors"
                >
                  <ChevronLeft size={20} className="text-gray-700 dark:text-gray-300" />
                </button>
              </div>
            )}
          </>
        ) : (
          <GlassCard className="p-16 text-center border-dashed !border-gray-300 dark:!border-gray-700">
            <div className="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/50 dark:bg-gray-800/50 mb-6 shadow-inner">
              <SearchIcon className="w-10 h-10 text-gray-400" />
            </div>
            <h3 className="text-2xl font-bold text-gray-900 dark:text-white mb-3">نتیجه‌ای یافت نشد</h3>
            <button 
              onClick={clearFilters}
              className="mt-4 px-6 py-2.5 bg-blue-50/50 text-blue-600 rounded-xl font-medium border border-blue-100/50"
            >
              پاک کردن همه فیلترها
            </button>
          </GlassCard>
        )}
      </div>
    </div>
  );
}
