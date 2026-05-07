export function parseDb(text: string) {
  const lines = text.split('\n').filter((l) => l.trim().length > 0);
  const dataLines = lines.slice(1);

  return dataLines.map((line, index) => {
    const parts: string[] = [];
    let inQuotes = false;
    let currentPart = "";
    
    // Remove the leading "1: " or "2: " from console output or raw file index
    let dataStr = line.replace(/^\d+:\s*/, '');
    
    for (let i = 0; i < dataStr.length; i++) {
      const char = dataStr[i];
      if (char === '"') {
        inQuotes = !inQuotes;
      } else if (char === ',' && !inQuotes) {
        parts.push(currentPart);
        currentPart = "";
      } else {
        currentPart += char;
      }
    }
    parts.push(currentPart);
    
    // Safety check, pad array if shorter
    while(parts.length < 24) parts.push("");

    const [
      codeIsar, nationalId, veteranStatus, firstName, lastName, fatherName,
      gender, nationality, religion, birthDateStr, martyrdomDateStr, age,
      birthPlace, fileLocation, burialPlace, education, occupation, maritalStatus,
      servingUnit, membershipType, eventStream, operationZone, enemy, militaryOperation
    ] = parts.map((p) => p.trim());

    let bYear = "", bMonth = "", bDay = "";
    if (birthDateStr && birthDateStr.includes("/")) {
      const dParts = birthDateStr.split("/");
      if (dParts.length === 3) {
        bYear = dParts[0];
        bMonth = dParts[1];
        bDay = dParts[2];
      }
    }

    let mYear = "", mMonth = "", mDay = "";
    if (martyrdomDateStr && martyrdomDateStr.includes("/")) {
      const dParts = martyrdomDateStr.split("/");
      if (dParts.length === 3) {
        mYear = dParts[0];
        mMonth = dParts[1];
        mDay = dParts[2];
      }
    }

    return {
      id: (index + 1).toString(),
      codeIsar: codeIsar || "",
      nationalId: nationalId || "",
      veteranStatus: veteranStatus || "",
      firstName: firstName || "",
      lastName: lastName || "",
      fatherName: fatherName || "",
      gender: gender || "",
      nationality: nationality || "",
      religion: religion || "",
      birthDate: birthDateStr || "",
      birthYear: bYear,
      birthMonth: bMonth,
      birthDay: bDay,
      martyrdomDate: martyrdomDateStr || "",
      martyrdomYear: mYear,
      martyrdomMonth: mMonth,
      martyrdomDay: mDay,
      age: age ? parseFloat(age) : null,
      birthPlace: birthPlace || "",
      fileLocation: fileLocation || "",
      burialPlace: burialPlace || "",
      education: education || "",
      occupation: occupation || "",
      maritalStatus: maritalStatus || "",
      servingUnit: servingUnit || "",
      membershipType: membershipType || "",
      eventStream: eventStream || "",
      operationZone: operationZone || "",
      enemy: enemy || "",
      militaryOperation: militaryOperation || "",
      profileImage: "", // Required empty field for image links
    };
  });
}
