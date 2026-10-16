#!/usr/bin/env python3
import argparse
import json
import shutil
import sys
from pathlib import Path


BRIDGE = """process.env.LAMBDA_TASK_ROOT = process.env.LAMBDA_TASK_ROOT || "/var/task";
const orig = require("./launcher-orig.js");

exports.launcher = async (event, context) => {
  const normalizedEvent = { ...event };
  if (Array.isArray(normalizedEvent.body)) {
    normalizedEvent.body = Buffer.from(normalizedEvent.body);
  } else if (typeof normalizedEvent.body === "string") {
    normalizedEvent.body = Buffer.from(
      normalizedEvent.body,
      normalizedEvent.isBase64Encoded ? "base64" : "utf8"
    );
  } else {
    normalizedEvent.body = undefined;
  }

  const headers = normalizedEvent.headers || {};
  normalizedEvent.host =
    normalizedEvent.host || headers["x-forwarded-host"] || headers.host;

  return orig.launcher(normalizedEvent, context);
};
"""

ASSET_DIRECTORIES = ("css", "images", "certificate", "videos", "works")
MINIMUM_PHP_FUNCTIONS = 8


def patch_functions(output_dir: Path) -> int:
    functions_dir = output_dir / "functions"
    patched = 0

    for config_path in functions_dir.rglob(".vc-config.json"):
        function_dir = config_path.parent
        launcher_path = function_dir / "launcher.js"
        original_path = function_dir / "launcher-orig.js"
        config = json.loads(config_path.read_text(encoding="utf-8"))

        if config.get("handler") != "launcher.launcher":
            continue
        if not launcher_path.is_file():
            raise RuntimeError(f"Missing PHP launcher: {launcher_path}")
        if original_path.exists():
            raise RuntimeError(f"Refusing to overwrite existing file: {original_path}")

        launcher_path.replace(original_path)
        launcher_path.write_text(BRIDGE, encoding="utf-8", newline="\n")
        config["handler"] = "launcher.js"
        config["launcherType"] = "Nodejs"
        config["awsLambdaHandler"] = "launcher.launcher"
        config_path.write_text(
            json.dumps(config, indent=2) + "\n", encoding="utf-8", newline="\n"
        )
        patched += 1

    if patched < MINIMUM_PHP_FUNCTIONS:
        raise RuntimeError(
            f"Expected at least {MINIMUM_PHP_FUNCTIONS} PHP functions, patched {patched}. "
            "The Vercel build output may have changed."
        )
    return patched


def copy_static_assets(project_dir: Path, output_dir: Path) -> int:
    static_dir = output_dir / "static"
    copied = 0

    for directory in ASSET_DIRECTORIES:
        source = project_dir / "api" / directory
        if not source.is_dir():
            raise RuntimeError(f"Missing project asset directory: {source}")

        destination = static_dir / directory
        if destination.exists():
            raise RuntimeError(f"Refusing to overwrite existing static assets: {destination}")
        shutil.copytree(source, destination)
        copied += sum(1 for item in destination.rglob("*") if item.is_file())

    return copied


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument(
        "output_dir",
        nargs="?",
        type=Path,
        default=Path(".vercel/output"),
    )
    parser.add_argument(
        "project_dir",
        nargs="?",
        type=Path,
        default=Path("."),
    )
    args = parser.parse_args()

    try:
        if not (args.output_dir / "config.json").is_file():
            raise RuntimeError(f"Missing Vercel Build Output API config in {args.output_dir}")
        patched = patch_functions(args.output_dir)
        copied = copy_static_assets(args.project_dir, args.output_dir)
    except (OSError, json.JSONDecodeError, RuntimeError) as error:
        print(f"PHP deployment patch failed: {error}", file=sys.stderr)
        return 1

    print(f"Patched {patched} PHP functions and copied {copied} static assets.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
