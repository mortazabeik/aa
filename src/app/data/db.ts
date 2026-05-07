import dbText from "../imports/db.txt?raw";
import { parseDb } from "../utils/parseDb";

const DB_KEY = "golzar_torbat_db";
const SUBMISSIONS_KEY = "golzar_torbat_submissions";
const SLIDERS_KEY = "golzar_torbat_sliders";

export interface Martyr {
  id: string;
  codeIsar: string;
  nationalId: string;
  veteranStatus: string;
  firstName: string;
  lastName: string;
  fatherName: string;
  gender: string;
  nationality: string;
  religion: string;
  birthDate: string;
  birthYear: string;
  birthMonth: string;
  birthDay: string;
  martyrdomDate: string;
  martyrdomYear: string;
  martyrdomMonth: string;
  martyrdomDay: string;
  age: number | null;
  birthPlace: string;
  fileLocation: string;
  burialPlace: string;
  education: string;
  occupation: string;
  maritalStatus: string;
  servingUnit: string;
  membershipType: string;
  eventStream: string;
  operationZone: string;
  enemy: string;
  militaryOperation: string;
  profileImage: string;
}

export interface Submission {
  id: string;
  type: string; // 'image', 'document', 'video', etc.
  martyrId: string;
  fileUrl: string;
  status: 'pending' | 'approved' | 'rejected';
  submittedAt: string;
}

export interface Slider {
  id: string;
  url: string;
  order: number;
}

export function getMartyrs(): Martyr[] {
  try {
    const stored = localStorage.getItem(DB_KEY);
    if (stored) {
      return JSON.parse(stored);
    }
  } catch (e) {
    console.error("Failed to parse stored DB, reverting to initial DB", e);
  }

  const initialData = parseDb(dbText);
  localStorage.setItem(DB_KEY, JSON.stringify(initialData));
  return initialData as Martyr[];
}

export function updateMartyr(updated: Martyr) {
  const current = getMartyrs();
  const index = current.findIndex(m => m.id === updated.id);
  if (index !== -1) {
    current[index] = updated;
    localStorage.setItem(DB_KEY, JSON.stringify(current));
  }
}

export function deleteMartyr(id: string) {
  const current = getMartyrs();
  const next = current.filter(m => m.id !== id);
  localStorage.setItem(DB_KEY, JSON.stringify(next));
}

export function addSubmission(sub: Omit<Submission, "id" | "status" | "submittedAt">) {
  const subs = getSubmissions();
  const newSub: Submission = {
    ...sub,
    id: Date.now().toString(),
    status: 'pending',
    submittedAt: new Date().toISOString(),
  };
  subs.push(newSub);
  localStorage.setItem(SUBMISSIONS_KEY, JSON.stringify(subs));
  return newSub;
}

export function getSubmissions(): Submission[] {
  try {
    const stored = localStorage.getItem(SUBMISSIONS_KEY);
    if (stored) {
      return JSON.parse(stored);
    }
  } catch (e) {}
  return [];
}

export function deleteSubmission(id: string) {
  const subs = getSubmissions();
  const next = subs.filter(s => s.id !== id);
  localStorage.setItem(SUBMISSIONS_KEY, JSON.stringify(next));
}

export function approveSubmission(id: string) {
  const subs = getSubmissions();
  const index = subs.findIndex(s => s.id === id);
  if (index !== -1) {
    subs[index].status = 'approved';
    localStorage.setItem(SUBMISSIONS_KEY, JSON.stringify(subs));
  }
}

const defaultSliders: Slider[] = [
  { id: "1", url: "slider/1.jpg", order: 0 },
  { id: "2", url: "slider/2.jpg", order: 1 },
  { id: "3", url: "slider/3.jpg", order: 2 },
];

export function getSliders(): Slider[] {
  try {
    const stored = localStorage.getItem(SLIDERS_KEY);
    if (stored) {
      return JSON.parse(stored).sort((a: Slider, b: Slider) => a.order - b.order);
    }
  } catch (e) {}
  
  localStorage.setItem(SLIDERS_KEY, JSON.stringify(defaultSliders));
  return defaultSliders;
}

export function addSlider(url: string) {
  const sliders = getSliders();
  const nextOrder = sliders.length > 0 ? Math.max(...sliders.map(s => s.order)) + 1 : 0;
  const newSlider: Slider = {
    id: Date.now().toString(),
    url,
    order: nextOrder
  };
  sliders.push(newSlider);
  localStorage.setItem(SLIDERS_KEY, JSON.stringify(sliders));
  return newSlider;
}

export function deleteSlider(id: string) {
  let sliders = getSliders();
  sliders = sliders.filter(s => s.id !== id);
  localStorage.setItem(SLIDERS_KEY, JSON.stringify(sliders));
}

export function reorderSliders(sliders: Slider[]) {
  const updated = sliders.map((s, index) => ({ ...s, order: index }));
  localStorage.setItem(SLIDERS_KEY, JSON.stringify(updated));
}