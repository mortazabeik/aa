import { useParams, Link, Navigate } from "react-router";
import { GlassCard } from "../components/GlassCard";
import { ImageWithRoseFallback } from "../components/ImageWithRoseFallback";
import { getMartyrs } from "../data/db";
import { ArrowRight, Calendar, MapPin, Heart, Info, Briefcase, Award, Users } from "lucide-react";
import { motion } from "motion/react";
import { useEffect } from "react";

export function MartyrDetail() {
  const { id } = useParams();
  const martyr = getMartyrs().find((m) => m.id === id);

  useEffect(() => {
    if (martyr) {
      document.title = `گلزار تربت - شهید ${martyr.firstName} ${martyr.lastName}`;
    }
  }, [martyr]);

  if (!martyr) {
    return <Navigate to="/search" replace />;
  }

  const fullName = `${martyr.firstName} ${martyr.lastName}`;

  const InfoItem = ({ icon: Icon, label, value, colorClass }: any) => {
    if (!value) return null;
    return (
      <div className="flex items-start gap-3">
        <div className={`p-2 rounded-lg ${colorClass}`}>
          <Icon size={20} />
        </div>
        <div>
          <p className="text-sm text-gray-500 dark:text-gray-400 mb-1">{label}</p>
          <p className="font-medium text-gray-900 dark:text-white leading-relaxed">{value}</p>
        </div>
      </div>
    );
  };

  return (
    <motion.div 
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="max-w-5xl mx-auto space-y-8"
    >
      <Link 
        to="/search" 
        className="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
      >
        <ArrowRight size={20} />
        بازگشت به نتایج
      </Link>

      <GlassCard className="overflow-hidden p-0 !rounded-3xl">
        <div className="flex flex-col md:flex-row">
          <div className="w-full md:w-1/3">
            <div className="relative w-full aspect-[3/4]">
              <ImageWithRoseFallback 
                src={martyr.profileImage} 
                alt={fullName} 
                className="absolute inset-0 w-full h-full object-cover"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent md:hidden" />
              <div className="absolute bottom-6 right-6 md:hidden">
                <h1 className="text-3xl font-bold text-white mb-2">{fullName}</h1>
                <p className="text-gray-300 font-medium">{martyr.servingUnit}</p>
              </div>
            </div>
          </div>
          
          <div className="w-full md:w-2/3 p-6 md:p-10 flex flex-col justify-center bg-white/40 dark:bg-black/20 backdrop-blur-md">
            <div className="hidden md:block mb-8">
              <h1 className="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                {fullName}
              </h1>
              {martyr.fatherName && (
                <p className="text-lg text-gray-600 dark:text-gray-300 mb-2 font-medium">
                  فرزند: {martyr.fatherName}
                </p>
              )}
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-8 mb-8">
              <InfoItem 
                icon={Award} label="کد ایثار" value={martyr.codeIsar} 
                colorClass="bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400" 
              />
              <InfoItem 
                icon={Info} label="ایثارگری" value={martyr.veteranStatus} 
                colorClass="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400" 
              />
              <InfoItem 
                icon={Calendar} label="تاریخ ولادت" value={martyr.birthDate} 
                colorClass="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400" 
              />
              <InfoItem 
                icon={Heart} label="تاریخ شهادت" value={martyr.martyrdomDate} 
                colorClass="bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400" 
              />
              <InfoItem 
                icon={Info} label="سن در زمان شهادت" value={martyr.age ? `${martyr.age} سال` : ""} 
                colorClass="bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400" 
              />
              <InfoItem 
                icon={MapPin} label="محل تولد" value={martyr.birthPlace} 
                colorClass="bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400" 
              />
              <InfoItem 
                icon={MapPin} label="محل گلزار" value={martyr.burialPlace} 
                colorClass="bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400" 
              />
              <InfoItem 
                icon={MapPin} label="محل پرونده" value={martyr.fileLocation} 
                colorClass="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400" 
              />
            </div>

            <div className="border-t border-gray-200/50 dark:border-gray-700/50 pt-8 mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-6">
              <InfoItem 
                icon={Users} label="جنسیت" value={martyr.gender} 
                colorClass="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400" 
              />
              <InfoItem 
                icon={Info} label="ملیت" value={martyr.nationality} 
                colorClass="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400" 
              />
              <InfoItem 
                icon={Info} label="مذهب" value={martyr.religion} 
                colorClass="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400" 
              />
              <InfoItem 
                icon={Info} label="تحصیلات" value={martyr.education} 
                colorClass="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400" 
              />
              <InfoItem 
                icon={Briefcase} label="شغل قبل از شهادت" value={martyr.occupation} 
                colorClass="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400" 
              />
              <InfoItem 
                icon={Users} label="وضعیت تاهل" value={martyr.maritalStatus} 
                colorClass="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400" 
              />
            </div>

            <div className="border-t border-gray-200/50 dark:border-gray-700/50 pt-8 mt-6 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-6">
              <InfoItem 
                icon={Award} label="یگان خدمت" value={martyr.servingUnit} 
                colorClass="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400" 
              />
              <InfoItem 
                icon={Info} label="نوع عضویت" value={martyr.membershipType} 
                colorClass="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400" 
              />
              <InfoItem 
                icon={Info} label="جریان رخداد" value={martyr.eventStream} 
                colorClass="bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400" 
              />
              <InfoItem 
                icon={MapPin} label="منطقه عملیاتی" value={martyr.operationZone} 
                colorClass="bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400" 
              />
              <InfoItem 
                icon={Info} label="دشمن" value={martyr.enemy} 
                colorClass="bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400" 
              />
              <InfoItem 
                icon={Info} label="عملیات نظامی" value={martyr.militaryOperation} 
                colorClass="bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400" 
              />
            </div>
          </div>
        </div>
      </GlassCard>
    </motion.div>
  );
}
