import pandas as pd
from pathlib import Path

csv_dir = Path("tests/reports")
out_xlsx = csv_dir / "test_report.xlsx"
csv_dir.mkdir(parents=True, exist_ok=True)

csv_files = sorted(csv_dir.glob("*.csv"))
if not csv_files:
    print("Tidak ada CSV di", csv_dir.resolve())
    raise SystemExit(1)

with pd.ExcelWriter(out_xlsx, engine="openpyxl") as writer:
    for p in csv_files:
        df = pd.read_csv(p)
        sheet_name = p.stem[:31]
        df.to_excel(writer, sheet_name=sheet_name, index=False)

print("Generated:", out_xlsx)
